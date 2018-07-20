
## Instgram API integration with Elastic search and Laravel framework

#### Please follow the setup procedure

Step 1: Clone the repo
```
git clone https://github.com/nsatheesh87/php-elasticsearch.git
```

Step 2: Open your working directory
```
cd YOUR_WORKING_DIRECTORY
```
Step 3: Run the docker compose
````
docker-compose up -d
````
Step 4: To run the composer
````
docker run --rm -v $(PWD):/app koutsoumpos89/composer-php7.1 install
````

Step 5: Copy .env.example to .env and Update the INSTAGRAM Access token value
````
Replace YOUR_INSTAGRAM_ACCESS_TOKEN_GOES_HERE with your access token in .env file
````

Step 6: Update application secret key (Windows host) or try without winpty for non-windows host
````
winpty docker exec -it 9gag_app_1 php artisan key:generate
````

Step 7: Import Instagram feed into your elasticsearch
````
End Point: http://localhost:8080/api/v1/instagram/feed
````

Step 8: All set.. 
````
http://localhost:8080
````
