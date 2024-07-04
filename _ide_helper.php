<?php

namespace Illuminate\Database\Eloquent {
    /**
     * Additions to the Eloquent Builder class from the EloquentBuilderMacro class.
     * 
     * @method \Illuminate\Database\Eloquent\Builder whereEncrypted(string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method \Illuminate\Database\Eloquent\Builder orWhereEncrypted(string $column, $operator = null, $value = null)
     * @method \Illuminate\Database\Eloquent\Builder whereEncryptedIn(string $column, array $values)
     * @method \Illuminate\Database\Eloquent\Builder orWhereEncryptedIn(string $column, array $values)
     * @method \Illuminate\Database\Eloquent\Builder whereEncryptedNotIn(string $column, array $values)
     * @method \Illuminate\Database\Eloquent\Builder orWhereEncryptedNotIn(string $column, array $values)
     * @method \Illuminate\Database\Eloquent\Builder whereEncryptedRelation($relation, $column, $operator = null, $value = null)
     * @method \Illuminate\Database\Eloquent\Builder orWhereEncryptedRelation($relation, $column, $operator = null, $value = null)
     * @method \Illuminate\Database\Eloquent\Builder search(array $columns, mixed $value)
     * @method \Illuminate\Database\Eloquent\Builder withMedia(...$collectionName)
     * 
     * @see \Pkt\StarterKit\Macros\EloquentBuilderMacro
     */
    class Builder {}
}

namespace Illuminate\Database\Query {
    /**
     * Additions to the Query Builder class from the QueryBuilderMacro class.
     * 
     * @method \Illuminate\Database\Query\Builder whereEncrypted(string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method \Illuminate\Database\Query\Builder orWhereEncrypted(string $column, $operator = null, $value = null)
     * @method \Illuminate\Database\Query\Builder whereEncryptedIn(string $column, array $values)
     * @method \Illuminate\Database\Query\Builder orWhereEncryptedIn(string $column, array $values)
     * @method \Illuminate\Database\Query\Builder whereEncryptedNotIn(string $column, array $values)
     * @method \Illuminate\Database\Query\Builder orWhereEncryptedNotIn(string $column, array $values)
     * @method \Illuminate\Database\Query\Builder whereEncryptedRelation($relation, $column, $operator = null, $value = null)
     * @method \Illuminate\Database\Query\Builder orWhereEncryptedRelation($relation, $column, $operator = null, $value = null)
     * @method \Illuminate\Database\Query\Builder search(array $columns, mixed $value)
     * 
     * @see \Pkt\StarterKit\Macros\QueryBuilderMacro
     */
    class Builder {}
}

namespace Illuminate\Database\Schema {
    /**
     * Additions to the Schema Blueprint class from the SchemaBlueprintMacro class.
     * 
     * @method void createdUpdatedBy()
     * @method void dropCreatedUpdatedBy()
     * @method \Illuminate\Database\Schema\Blueprint encrypted(string $column)
     * 
     * @see \Pkt\StarterKit\Macros\SchemaBlueprintMacro
     */
    class Blueprint {}
}

namespace Illuminate\Routing {
    /**
     * Additions to the Router class from the RouterMacro class.
     *
     * @method self authenticated()
     * @method self roles(String|array $roles)
     *
     * @see \Pkt\StarterKit\Macros\RouterMacro
     */
    class Router {}
}

namespace Illuminate\Routing {
    /**
     * Additions to the Route class from the RouterMacro class.
     *
     * @method self authenticated()
     * @method self roles(String|array $roles)
     *
     * @see \Pkt\StarterKit\Macros\RouterMacro
     */
    class Route {}
}
