<?php

namespace Pkt\StarterKit\Helpers;

use Doctrine\DBAL\Types\Types;

class FormBuilder
{
    private static string $form;
    private static string $modelName;
    private static string $column;
    private static string $label;
    private static string $required;

    public static function build(string $type, string $modelName, string $column, string $label, bool $required): string
    {
        self::$modelName = $modelName;
        self::$column = $column;
        self::$label = $label;
        self::$required = $required ? 'true' : 'false';

        switch ($type) {
            case Types::STRING:
                return self::setFormWithTypeString();
            case Types::TEXT:
                return self::setFormWithTypeText();
            case Types::INTEGER:
            case Types::FLOAT:
            case Types::DECIMAL:
                return self::setFormWithTypeNumber();
            case Types::BOOLEAN:
                return self::setFormWithTypeBoolean();
            case Types::DATE_MUTABLE:
                return self::setFormWithTypeDate();
            case Types::TIME_MUTABLE:
                return self::setFormWithTypeTime();
            case Types::DATETIME_MUTABLE:
                return self::setFormWithTypeDatetime();
            default:
                return self::setFormWithTypeString();
        }

        return self::$form;
    }

    /**
     * Set the form of type string
     *
     * @return  self
     */
    public static function setFormWithTypeString(): string
    {
        $modelName = self::$modelName;
        $column = self::$column;
        $label = self::$label;
        $required = self::$required;

        self::$form = "
                <el-form-item :error=\"getFormError('$column')\" prop=\"$column\" label=\"$label\" :required=\"$required\">
                    <el-input v-model=\"form$modelName.$column\" autocomplete=\"one-time-code\" autocorrect=\"off\" spellcheck=\"false\" />
                </el-form-item>" . PHP_EOL . '                ';

        return self::$form;
    }

    /**
     * Set the form of type text
     *
     * @return  self
     */
    public static function setFormWithTypeText(): string
    {
        $modelName = self::$modelName;
        $column = self::$column;
        $label = self::$label;
        $required = self::$required;

        self::$form = "
                <el-form-item :error=\"getFormError('$column')\" prop=\"$column\" label=\"$label\" :required=\"$required\">
                    <el-input type=\"textarea\" v-model=\"form$modelName.$column\" autocomplete=\"one-time-code\" autocorrect=\"off\" spellcheck=\"false\" />
                </el-form-item>" . PHP_EOL . '                ';

        return self::$form;
    }

    /**
     * Set the form of type number
     *
     * @return  self
     */
    public static function setFormWithTypeNumber(): string
    {
        $modelName = self::$modelName;
        $column = self::$column;
        $label = self::$label;
        $required = self::$required ;

        self::$form = "
                <el-form-item :error=\"getFormError('$column')\" prop=\"$column\" label=\"$label\" :required=\"$required\">
                    <el-input-number v-model=\"form$modelName.$column\" autocomplete=\"one-time-code\" autocorrect=\"off\" spellcheck=\"false\" />
                </el-form-item>" . PHP_EOL . '                ';

        return self::$form;
    }

    /**
     * Set the form of type boolean
     *
     * @return  self
     */
    public static function setFormWithTypeBoolean(): string
    {
        $modelName = self::$modelName;
        $column = self::$column;
        $label = self::$label;
        $required = self::$required;

        self::$form = "
                <el-form-item :error=\"getFormError('$column')\" prop=\"$column\" label=\"$label\" :required=\"$required\">
                    <el-switch v-model=\"form$modelName.$column\" active-color=\"#13ce66\" inactive-color=\"#ff4949\" :active-value=\"'1'\" :inactive-value=\"'0'\" />
                </el-form-item>" . PHP_EOL . '                ';

        return self::$form;
    }

    /**
     * Set the form of type date
     *
     * @return  self
     */
    public static function setFormWithTypeDate(): string
    {
        $modelName = self::$modelName;
        $column = self::$column;
        $label = self::$label;
        $required = self::$required;

        self::$form = "
                <el-form-item :error=\"getFormError('$column')\" prop=\"$column\" label=\"$label\" :required=\"$required\">
                    <el-date-picker v-model=\"form$modelName.$column\" type=\"date\" placeholder=\"Pick a date\" />
                </el-form-item>" . PHP_EOL . '                ';

        return self::$form;
    }

    /**
     * Set the form of type time
     *
     * @return  self
     */
    public static function setFormWithTypeTime(): string
    {
        $modelName = self::$modelName;
        $column = self::$column;
        $label = self::$label;
        $required = self::$required;

        self::$form = "
                <el-form-item :error=\"getFormError('$column')\" prop=\"$column\" label=\"$label\" :required=\"$required\">
                    <el-time-picker v-model=\"form$modelName.$column\" placeholder=\"Pick a time\" />
                </el-form-item>" . PHP_EOL . '                ';

        return self::$form;
    }

    /**
     * Set the form of type datetime
     *
     * @return  self
     */
    public static function setFormWithTypeDatetime(): string
    {
        $modelName = self::$modelName;
        $column = self::$column;
        $label = self::$label;
        $required = self::$required;

        self::$form = "
                <el-form-item :error=\"getFormError('$column')\" prop=\"$column\" label=\"$label\" :required=\"$required\">
                    <el-date-picker v-model=\"form$modelName.$column\" type=\"datetime\" placeholder=\"Pick a datetime\" />
                </el-form-item>" . PHP_EOL . '                ';

        return self::$form;
    }
}
