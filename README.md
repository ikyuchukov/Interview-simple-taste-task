**T&H Taste** 
=============

### How to start the app:
* Build docker image

        docker-compose build
* Start docker

        docker-compose up -d
        
* Enter the PHP container (or use docker exec for the other commands)

        docker exec -it taste-ilian-php bash
    note: prefix is used to not have conflicts with other candidate's docker images
        
* Install dependencies

        composer install
    
* Run migrations

        bin/console doctrine:migrations:migrate
    
    
