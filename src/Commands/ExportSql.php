<?php

namespace  Ake\Tools\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExportSql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:export-sql {--all} {--system}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export table structure
                        {--all 是否导出后台的admin表}
                        {--system 是否导出系统自带表}';

    #后台表
    protected $all = [
        'admin_extensions', 'admin_extension_histories', 'admin_menu', 'admin_permission_menu', 'admin_permissions', 'admin_role_menu', 'admin_role_permissions', 'admin_role_users', 'admin_roles', 'admin_users', 'admin_settings'
    ];

    #系统表
    protected $system = [
        'failed_jobs', 'migrations', 'password_resets', 'personal_access_tokens'
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $database_name = config('database.connections.mysql.database');
        $tables = array_map('reset', DB::select('SHOW TABLES'));
        #是否获取后台表
        if (!$this->option('all')){
            $tables = array_diff($tables,$this->all);
        }
        #是否获取系统表
        if (!$this->option('system')){
            $tables = array_diff($tables,$this->system);
        }
        $fp = fopen(public_path() . '/sql.csv', 'w');
        fputcsv($fp, ['', '字段名', '', '字段类型', '', '备注']);
        foreach ($tables as $value) {
            fputcsv($fp, [$value]);
            $res = DB::select("select  column_name , column_type ,column_comment   from information_schema.columns where table_schema ='$database_name'  and table_name = '$value'");
            foreach ($res as $v) {
                fputcsv($fp, ['', $v->column_name, '', $v->column_type, '', $v->column_comment]);
            }
            fputcsv($fp, ['']);
        }
        fclose($fp);
        $this->info('All exported successfully');
    }
}
