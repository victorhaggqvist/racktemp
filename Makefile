install:
	npm install;
	bower install;
	composer install;

clean:
	rm -rf npm_modules bower_components vendor;
	grunt clean;
