# Transaction commissions counter
This is solution to the task: https://gist.github.com/mariusbalcytis/e73370f4d2bda302c7bd867dfeef9751.

The app is based on PHP 7.4. To set it up download the project and run:
```
composer install
```

Launch the app on CLI by executing `app.php` and providing input file, e.g.:
```
 php app.php input.txt
```

Execute unit tests with command:
```
./vendor/bin/phpunit
```
## Notes
If you notice issue with file reading, adjust `NEW_LINE_DELIMITER` in `app/Repository/FileReader.php`, it is recognizing end-of-lines by `\r\n`. Or set EOL type in your input files accordingly.
