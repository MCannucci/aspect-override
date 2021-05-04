<?php

namespace AspectOverride\Filters;

class FunctionHookFilter extends AbstractFilter
{
    // These two need to both be the same amount of characters
    private const PRE_HOOK = /** @lang InjectablePHP */ <<<CODE
if(\$__fn__ = \AspectOverride\Facades\Registry::getForClass(__CLASS__,__FUNCTION__)) { return \$__fn__(...func_get_args()); }
CODE;
    private const PRE_HOOK_NO_RETURN = /** @lang InjectablePHP */ <<<CODE
if(\$__fn__ = \AspectOverride\Facades\Registry::getForClass(__CLASS__,__FUNCTION__)) { \$__fn__(...func_get_args()); return;}
CODE;

    public function getName(): string
    {
        return "FUNCTION_HOOK";
    }

    public function process(string $chunk, int $length)
    {
        // Oh god, oh shit. Forgive me for using regex to modify PHP
        $re = '/(function.+?{)(\s+)([^\s\\\\])/s';
        preg_match_all($re, $chunk, $matches, PREG_OFFSET_CAPTURE, 0);
        if ($matches) {
            $function = $matches[1];
            $matches = $matches[3]; // Only interested in the last match
            /**
             * Each time we insert our code it will bump everything forward causing the previously
             * found positions to become invalid, we have to apply this "bump" manually ourselves
             */
            $matchesLength = count($matches);
            $bumpCount = strlen(self::PRE_HOOK);
            for($i = 0; $i < $matchesLength; $i++) {
                $shouldOmitReturn = preg_match("/\).+void/", $function[$i][0]);
                $match = $matches[$i];
                $pos = ($match[1] + ($bumpCount * $i)) - 1;
                /**
                 * Insert the "before" function hook before the first variable/statement
                 */
                $code = $shouldOmitReturn ? self::PRE_HOOK_NO_RETURN : self::PRE_HOOK;
                $chunk = substr($chunk, 0, $pos) . $code . substr($chunk, $pos);
            }
        }
        return $chunk;
    }
}