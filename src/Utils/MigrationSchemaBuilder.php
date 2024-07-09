<?php

namespace Pkt\StarterKit\Utils;

class MigrationSchemaBuilder
{
    /**
     * Add a new column to the table
     *
     * @param string $columnName
     * @param string $dataType
     * @param array $options
     * @return string
     */
    public static function addColumn(string $columnName, string $dataType, array $options = []): string
    {
        $schema = '$table->'.$dataType.'(\''.$columnName.'\''.self::parseDataTypeAdditionalParams($dataType).')';
        if (!empty($options)) {
            $schema .= self::parseOptions($options). ';';
        } else {
            $schema .= ';';
        }

        return $schema;
    }

    /**
     * Drop a column from the table
     *
     * @param string $columnName
     * @return string
     */
    public static function dropColumn(string $columnName): string
    {
        return '$table->dropColumn(\''.$columnName.'\');';
    }

    /**
     * Rename a column in the table
     *
     * @param string $oldColumnName
     * @param string $newColumnName
     * @return string
     */
    public static function renameColumn(string $oldColumnName, string $newColumnName): string
    {
        return '$table->renameColumn(\''.$oldColumnName.'\', \''.$newColumnName.'\');';
    }

    /**
     * Change the data type of a column
     *
     * @param string $columnName
     * @param string $dataType
     * @param array $options
     * @return string
     */
    public static function changeColumnDataType(string $columnName, string $dataType, array $options = []): string
    {
        $schema = '$table->'.$dataType.'(\''.$columnName.'\''.self::parseDataTypeAdditionalParams($dataType).')';
        if (!empty($options)) {
            $schema .= self::parseOptions($options) . '->change();';
        } else {
            $schema .= '->change();';
        }

        return $schema;
    }

    /**
     * Parse the options for the column
     *
     * @param array $options
     * @return string
     */
    private static function parseOptions(array $options = []): string
    {
        $schema = '';
        if (in_array('after', $options)) {
            $schema .= '->after(\'column_name\')';
        }
        if (in_array('autoIncrement', $options)) {
            $schema .= '->autoIncrement()';
        }
        if (in_array('charset', $options)) {
            $schema .= '->charset(\'utf8\')';
        }
        if (in_array('comment', $options)) {
            $schema .= '->comment(\'comment\')';
        }
        if (in_array('default', $options)) {
            $schema .= '->default(\'value\')';
        }
        if (in_array('first', $options)) {
            $schema .= '->first()';
        }
        if (in_array('nullable', $options)) {
            $schema .= '->nullable()';
        }
        if (in_array('unique', $options)) {
            $schema .= '->unique()';
        }

        return $schema;
    }

    /**
     * Parse the additional parameters for the data type
     *
     * @param string $dataType
     * @return string
     */
    private static function parseDataTypeAdditionalParams(string $dataType): string
    {
        $schema = '';
        if ($dataType === 'decimal') {
            $schema .= ', 8, 2';
        } elseif ($dataType === 'enum') {
            $schema .= ', [1,2]';
        }

        return $schema;
    }
}