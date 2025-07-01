# WHAT TO DO

```bash
git remote add github_origin git@github.com:nikic/PHP-Parser.git
git pull github_origin v5.5.0
```

switch to php 7.4
```bash
sh ~/.ssh/php.sh 74
```

resolve conflicts
```bash
php grammar/rebuildParsers.php
```

```bash
php bin/test.php
```

create pull request from `mrsuh/generics-support` to `mrsuh/master`
