**T&H Taste** 
=============

### How to start the app:
* Build docker image

        docker-compose build
* Start docker

        docker-compose up -d
        
* Install dependencies

        docker exec -it taste-ilian-php composer install
    note: prefix is used to not have conflicts with other candidate's docker images
    
