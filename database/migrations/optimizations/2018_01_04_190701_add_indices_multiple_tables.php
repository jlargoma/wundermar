<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndicesMultipleTables extends Migration
{
    /**
     * Add indices
     *
     * @return string
     */
    public function up()
    {
        foreach($this->getTablesAndColumns() as $tableName => $columns) {
            \Schema::table($tableName, function(Blueprint $table) use($columns) {
                foreach ($columns as $column) {
                    list($columnName, $indexType) = explode('|', $column);
                    $table->{$indexType}($columnName);
                }
            });    
        }

        dump('Added indices to ' . $this->getTableNames());
    }

    /**
     * Remove indices
     *
     * @return string
     */
    public function down()
    {
        foreach($this->getTablesAndColumns() as $tableName => $columns) {
            \Schema::table($tableName, function(Blueprint $table) use($columns, $tableName) {
                foreach ($columns as $column) {
                    list($columnName, $indexType) = explode('|', $column);
                    $indexName = "{$tableName}_{$columnName}_{$indexType}"; // book_start_index
                    $dropMethod = 'drop' . ucfirst($indexType);             // dropIndex
                    $table->{$dropMethod}($indexName);
                }
            });    
        }

        dump('Dropped indices from: ' . $this->getTableNames());
    }

    /**
     * Returns and array of tables and column names with proper index to add.
     * @return array
     */
    private function getTablesAndColumns()
    {
        return [
            'book' => ['start|index', 'finish|index', 'type_book|index'],
            'rooms' => ['state|index'],
            'payments' => ['comment|index', 'created_at|index'],
        ];
    }

    /**
     * @return string
     */
    private function getTableNames()
    {
        return implode(', ', array_keys($this->getTablesAndColumns()));
    }
}
