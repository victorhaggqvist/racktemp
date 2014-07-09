install:
	npm install;
	bower install;
	composer install;

clean:
	rm -rf npm_modules bower_components vendor;
	grunt clean;

pushdev:
	grunt;
	git add app/js app/style;
	git commit -m "assets update";
	git push github dev;
