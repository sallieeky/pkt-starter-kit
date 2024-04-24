<?php

namespace Pkt\StarterKit\Utils\Enums;

enum ColumnType: string
{
    case CHAR = 'char';
    case INTEGER = 'int';
    case BIGINTEGER = 'bigint';
    case SMALLINTEGER = 'smallint';
    case FLOAT = 'double';
    case TINYINT = 'tinyint';
    case DECIMAL = 'decimal';
    case STRING = 'string';
    case BOOLEAN = 'boolean';
    case TEXT = 'text';
    case ENUM = 'enum';
    case JSON = 'json';
    case DATE = 'date';
    case DATETIME = 'datetime';
    case TIME = 'time';
}
