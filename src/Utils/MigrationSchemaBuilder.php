<?php

namespace Pkt\StarterKit\Utils;

class MigrationSchemaBuilder
{
    public static function addColumn(string $columnName, string $dataType, array $options = []): string
    {
        $schema = '$table->'.$dataType.'(\''.$columnName.self::parseDataTypeAdditionalParams($dataType) .'\')';
        if (!empty($options)) {
            $schema .= self::parseOptions($options). ';';
        } else {
            $schema .= ';';
        }

        return $schema;
    }

    public static function dropColumn(string $columnName): string
    {
        return '$table->dropColumn(\''.$columnName.'\');';
    }

    public static function renameColumn(string $oldColumnName, string $newColumnName): string
    {
        return '$table->renameColumn(\''.$oldColumnName.'\', \''.$newColumnName.'\');';
    }

    public static function changeColumnDataType(string $columnName, string $dataType, array $options = []): string
    {
        $schema = '$table->'.$dataType.'(\''.$columnName.self::parseDataTypeAdditionalParams($dataType) .'\')';
        if (!empty($options)) {
            $schema .= self::parseOptions($options) . '->change();';
        } else {
            $schema .= '->change();';
        }

        return $schema;
    }

    private static function parseOptions(array $options = []): string
    {
        $schema = '';
        if (in_array('nullable', $options)) {
            $schema .= '->nullable()';
        } 
        if (in_array('unique', $options)) {
            $schema .= '->unique()';
        }
        if (in_array('default', $options)) {
            $schema .= '->default(null)';
        }

        return $schema;
    }

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