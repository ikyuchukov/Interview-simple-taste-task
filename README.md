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
        
* Create fixtures (you can remove the argument to use default of 100)

        bin/console app:create-course-fixtures 200
    
* Register at http://127.0.0.1:8080/register
* Login at http://127.0.0.1:8080/login
* Courses are available at http://127.0.0.1:8080/home    

* To set your user as admin you can run the following query (change id accordingly)

        INSERT INTO taste.user_roles
        (user_id, `role`)
        VALUES(1, 'admin');

* bin/phpunit to run tests
