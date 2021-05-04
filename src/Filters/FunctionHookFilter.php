<?php

namespace AspectOverride\Filters;

class FunctionHookFilter extends AbstractFilter
{

    private const PRE_HOOK = /** @lang InjectablePHP */ <<<CODE
if(\$__fn__ = \AspectOverride\Facades\Registry::getForClass(__CLASS__,__FUNCTION__)) { return \$__fn__(...func_get_args()); }
CODE;
    private const PRE_HOOK_NO_RETURN = /** @lang InjectablePHP */ <<<CODE
if(\$__fn__ = \AspectOverride\Facades\Registry::getForClass(__CLASS__,__FUNCTION__)) { \$__fn__(...func_get_args()); return;}
CODE;
    private const TOKEN_CONTENT = 1;
    private const TOKEN_NAME = 0;

    public function getName(): string
    {
        return "FUNCTION_HOOK";
    }

    public function process(string $chunk, int $length)
    {
      $tokens = token_get_all($chunk, TOKEN_PARSE);
      unset($chunk);
      $buffer = "";
      $totalTokens = count($tokens);
      for ($i = 0; $i < $totalTokens; $i++) {
        [$content, $type] = $this->normalizeToken($tokens[$i]);
        if(T_PUBLIC === $type || T_PROTECTED === $type || T_PRIVATE === $type) {
          $shouldNotReturnAfterIntercept = false;
          // Keep consuming tokens until we've hit the first openning brace to denote the end of the function declaration
          do {
            // Hack, we're probably in an abstract function
            if(!array_key_exists($i, $tokens)) {
              break;
            }
            [$content, $type] = $this->normalizeToken($tokens[$i]);
            $buffer .= $content;
            // I think it's safe to assume if we hit void here, it's only in the context of a function declaration
            if($content === 'void') {
              $shouldNotReturnAfterIntercept = true;
            }
            $i++;
          } while ($type !== '{');
          // Keep consuming tokens until we've hit a non comment/whitespace to denote the start of the implementation
          do {
            // Hack, we're probably in an abstract function
            if(!array_key_exists($i, $tokens)) {
              break;
            }
            [$content, $type] = $this->normalizeToken($tokens[$i]);
            $nonStatement = $type === '{' || $type === T_WHITESPACE || $type === T_COMMENT || $type === T_DOC_COMMENT;
            if(!$nonStatement) {
              $buffer .= ($shouldNotReturnAfterIntercept ?
                self::PRE_HOOK_NO_RETURN : self::PRE_HOOK ) . $content;
              continue;
            }
            $buffer .= $content;
            $i++;
          } while ($nonStatement);
          continue;
        }
        $buffer .= $content;
      }
      return $buffer;
    }
    protected function normalizeToken($token) {
      return is_array($token) ? [$token[self::TOKEN_CONTENT], $token[self::TOKEN_NAME]] : [$token, $token];
    }
}
