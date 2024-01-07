## Coding Style

This project uses PSR-12 with consistent brace placement. This means that the opening brace is
always on the same line, even for class and method declarations.

## Tools

This project uses PHP-CS-Fixer and PHPStan. You can invoke them using `make`:

```shell
make php-cs-fixer
make phpstan
```

## Adding support for new PHP syntax

1. If necessary, add emulation support for new tokens.
   * Add a new subclass of `Lexer\TokenEmulator`. Take inspiration from existing classes.
   * Add the new class to the array in `Lexer\Emulative`.
   * Add tests for the emulation in `Lexer\EmulativeTest`. You'll want to modify
     `provideTestReplaceKeywords()` for new reserved keywords and `provideTestLexNewFeatures()` for
     other emulations.
2. Add any new node classes that are needed.
3. Add support for the new syntax in `grammar/php.y`. Regenerate the parser by running
   `php grammar/rebuildParsers.php`. Use `--debug` if there are conflicts.
4. Add pretty-printing support by implementing a `pFooBar()` method in `PrettyPrinter\Standard`.
5. Add tests both in `test/code/parser` and `test/code/prettyPrinter`.
6. Add support for formatting-preserving pretty-printing. This is done by modifying the data tables
   at the end of `PrettyPrinterAbstract`. Add a test in `test/code/formatPreservation`.
7. Does the new syntax feature namespaced names? If so, add support for name resolution in
   `NodeVisitor\NameResolver`. Test it in `NodeVisitor\NameResolverTest`.
8. Does the new syntax require any changes to builders? Is so, make them :)
