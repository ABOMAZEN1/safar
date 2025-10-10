<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\Set\ValueObject\LevelSetList;
// Code Quality rules
use Rector\CodeQuality\Rector\Assign\CombinedAssignRector;
use Rector\CodeQuality\Rector\BooleanAnd\SimplifyEmptyArrayCheckRector;
use Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector;
use Rector\CodeQuality\Rector\ClassMethod\InlineArrayReturnAssignRector;
use Rector\CodeQuality\Rector\Equal\UseIdenticalOverEqualWithSameTypeRector;
use Rector\CodeQuality\Rector\Expression\InlineIfToExplicitIfRector;
use Rector\CodeQuality\Rector\Foreach_\SimplifyForeachToCoalescingRector;
use Rector\CodeQuality\Rector\FuncCall\SimplifyStrposLowerRector;
use Rector\CodeQuality\Rector\FunctionLike\SimplifyUselessVariableRector;
use Rector\CodeQuality\Rector\If_\CombineIfRector;
use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;
use Rector\CodeQuality\Rector\If_\SimplifyIfElseToTernaryRector;
use Rector\CodeQuality\Rector\If_\SimplifyIfReturnBoolRector;
// Dead Code rules
use Rector\DeadCode\Rector\Array_\RemoveDuplicatedArrayKeyRector;
use Rector\DeadCode\Rector\Assign\RemoveDoubleAssignRector;
use Rector\DeadCode\Rector\Assign\RemoveUnusedVariableAssignRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveEmptyClassMethodRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedConstructorParamRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPrivateMethodRector;
use Rector\DeadCode\Rector\Expression\RemoveDeadStmtRector;
use Rector\DeadCode\Rector\For_\RemoveDeadLoopRector;
use Rector\DeadCode\Rector\FunctionLike\RemoveDeadReturnRector;
use Rector\DeadCode\Rector\Property\RemoveUnusedPrivatePropertyRector;
use Rector\DeadCode\Rector\PropertyProperty\RemoveNullPropertyInitializationRector;
// Early Return rules
use Rector\EarlyReturn\Rector\Foreach_\ChangeNestedForeachIfsToEarlyContinueRector;
use Rector\EarlyReturn\Rector\If_\ChangeIfElseValueAssignToEarlyReturnRector;
use Rector\EarlyReturn\Rector\If_\RemoveAlwaysElseRector;
use Rector\EarlyReturn\Rector\Return_\PreparedValueToEarlyReturnRector;
// Naming rules
use Rector\Naming\Rector\Assign\RenameVariableToMatchMethodCallReturnTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameVariableToMatchNewTypeRector;
use Rector\Naming\Rector\Foreach_\RenameForeachValueVariableToMatchMethodCallReturnTypeRector;
// PHP version and modern features
use Rector\Php73\Rector\FuncCall\JsonThrowOnErrorRector;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
// Privatization rules
use Rector\Privatization\Rector\ClassMethod\PrivatizeFinalClassMethodRector;
use Rector\Privatization\Rector\MethodCall\PrivatizeLocalGetterToPropertyRector;
use Rector\Privatization\Rector\Property\PrivatizeFinalClassPropertyRector;
// Type Declaration rules
use Rector\TypeDeclaration\Rector\ClassMethod\AddParamTypeDeclarationRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddReturnTypeDeclarationRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromReturnNewRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictTypedCallRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictTypedPropertyRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;

return static function (RectorConfig $rectorConfig): void {
    // Specify paths to analyze: include both legacy paths.
    $rectorConfig->paths([
        __DIR__ . '/app',
        __DIR__ . '/config',
        __DIR__ . '/database',
        __DIR__ . '/resources',
        __DIR__ . '/routes',
    ]);

    // Specify paths and patterns to skip.
    $rectorConfig->skip([
        __DIR__ . '/bootstrap/*.php',
        __DIR__ . '/config/*.php',
        __DIR__ . '/app/Providers/*.php',
        __DIR__ . '/database/migrations',
        __DIR__ . '/database/migrations/*.php',
        __DIR__ . '/app/Http/Requests/Api/BaseApiFormRequest.php',
        __DIR__ . '/app/Http/Controllers/Api/BaseApiController.php',

        // Skip a specific rule for certain paths.
        RemoveUnusedPrivateMethodRector::class => [
            __DIR__ . '/app/Jobs/*.php',
            __DIR__ . '/app/Listeners/*.php',
        ],
    ]);

    // Set the target PHP version (82000 corresponds to PHP 8.2.0).
    $rectorConfig->phpVersion(82000);

    // Register sets from both configurations.
    $rectorConfig->sets([
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::PHP_82,
        SetList::TYPE_DECLARATION,
        SetList::CODING_STYLE,
        SetList::NAMING,
        SetList::EARLY_RETURN,
        LevelSetList::UP_TO_PHP_84,
    ]);

    // Register individual rules.
    $rectorConfig->rules([
        // Type Declaration rules
        AddParamTypeDeclarationRector::class,
        AddReturnTypeDeclarationRector::class,
        ReturnTypeFromReturnNewRector::class,
        ReturnTypeFromStrictTypedCallRector::class,
        ReturnTypeFromStrictTypedPropertyRector::class,
        TypedPropertyFromStrictConstructorRector::class,

        // Code Quality rules
        CombinedAssignRector::class,
        SimplifyEmptyArrayCheckRector::class,
        CompleteDynamicPropertiesRector::class,
        InlineArrayReturnAssignRector::class,
        UseIdenticalOverEqualWithSameTypeRector::class,
        InlineIfToExplicitIfRector::class,
        SimplifyForeachToCoalescingRector::class,
        SimplifyStrposLowerRector::class,
        SimplifyUselessVariableRector::class,
        CombineIfRector::class,
        SimplifyIfElseToTernaryRector::class,
        SimplifyIfReturnBoolRector::class,
        ExplicitBoolCompareRector::class,

        // Dead Code rules
        RemoveDuplicatedArrayKeyRector::class,
        RemoveDoubleAssignRector::class,
        RemoveUnusedVariableAssignRector::class,
        RemoveEmptyClassMethodRector::class,
        // RemoveUnusedConstructorParamRector::class,
        RemoveUnusedPrivateMethodRector::class,
        RemoveDeadStmtRector::class,
        RemoveDeadLoopRector::class,
        RemoveDeadReturnRector::class,
        RemoveUnusedPrivatePropertyRector::class,
        RemoveNullPropertyInitializationRector::class,

        // Early Return rules
        ChangeNestedForeachIfsToEarlyContinueRector::class,
        ChangeIfElseValueAssignToEarlyReturnRector::class,
        RemoveAlwaysElseRector::class,
        PreparedValueToEarlyReturnRector::class,

        // Naming rules
        RenameVariableToMatchMethodCallReturnTypeRector::class,
        RenameParamToMatchTypeRector::class,
        RenameVariableToMatchNewTypeRector::class,
        RenameForeachValueVariableToMatchMethodCallReturnTypeRector::class,

        // Privatization rules
        PrivatizeFinalClassMethodRector::class,
        PrivatizeLocalGetterToPropertyRector::class,
        PrivatizeFinalClassPropertyRector::class,

        // Modern PHP Features
        ClosureToArrowFunctionRector::class,
        JsonThrowOnErrorRector::class,
    ]);

    // Additional Rector parameters.
    $rectorConfig->parallel();
    $rectorConfig->importNames();
    $rectorConfig->importShortClasses();

    // Ensure vendor autoloading.
    $rectorConfig->autoloadPaths([
        __DIR__ . '/vendor/autoload.php',
    ]);
};
