#Steps:

## A. Recreate The Code Base 
1. Make a directory under my name ```Tofighian```
    ```ssh
    mkdir Tofighian
    cd Tofighian
    ```
2. Copy the provided code base under my directory.
    ```ssh
    cp -R ../../project/. .
    ```
3. initial git repository
    ```ssh
    git init
    git add .
    git commit -m "Add challange 3 code base under Tofighian directory" 
    ```
    * **Note** that in this statge of my work, I have no intentios to apply for a pull request and I want to hand in my project as a ```.tar.zip``` file, so I only version-control my work :D
4. Make the docs directory and a new file called **Steps.md** that holds current data (Steps to fulfill the challange )
    * **Note**: here after I only document my version controls as below:

    | git message    | what have i done |
    |----------------|------------------|
    | Added: docs directory     | **docs** direcotry is added including **Steps.md** file (current file), so i can document my workflow and the way i approach each codding challange in my real life |

    * **Note**: I'll try to exclude ```bash``` codes like ```touch docs/Steps.md``` from my documentation.

## B. Make Docker Container
in order to run my code with the specifiied requirements mentioned in the **challange docs** like php 8, I made my own docker container, to encapsulate (*containerize*) my codes. 
* **Note**: I used **apache** as my webserver (I think it is a better choice in development stage rather than **nginx** because of lower configures required to be up and running and some compability issues)
* **Note**: Due to Iran's sactions, we could not use docker and debian using its own mirrors, so in my setup i changed the mirrors using ```sed``` bash command. 

    | git message    | what have i done |
    |----------------|------------------|
    | Added: docker containers and needed ```.env``` fields     | in this step multiple directories, docker-compose.yml and assocciated **Dockerfile**s are added to project and the project is ready to be ran. I also added a new get route to test if every thing is working correctly|

# C. Migrate Data, To DBMS
using below command the migrations are occured inside table ```dnj_challange_3```.
```ssh
php artisan migrate
```
* **Note**: because there are no changes here in my code, so there are no commits in the git repo. but I document this step as I do in real life.

***THIS WAS THE LAST STEP BEFORE I ACTUALLY START MY PROGRAMMING***

# D. PASSING TEST
Because the only resources we have to conclude what should be programmed are test cases and a brief readme file so I used the tests as a starting point to apporach the challange.

##1. AuthControllerTest

###1.1. testRegister

a) How to test? Instead of ```php artisan test``` I prefer to use ```phpunit --filter AuthControllerTest``` as it is obviouse in the changes i also commented out all other test cases.

b) Because the results have a missing property (*password*) it can be infered a verb-resource is needed. to be consistent with the tests, i generate an API resource: ```RegisterResource``` using below command:
```ssh
php artisan make:resource RegisterResource
```
It creates **RegisterResource.php** file inside **app/Http/Resources** directory. The ```toArray``` method would be overwritten as it is in the file, to hide password from returned results.

c) There are a RegisterRequest class inside Requests folder which can be used to validate the request. So I **type hint** it as an input argument, in the AuthController.
* **Note**: Fillables of the User should be changed as below:
    - name
    - **lastname**
    - **cellphone**
    - password
* Now is the time to commit the changes as below:

| git message    | what have i done |
|----------------|:-----------------|
| test passed: AuthControllerTest@testRegister | Changed: Fillables in the model. Added: RegisterResource. Changed: AuthController@register. Commented: Extra test cases inside the main test class |

###1.2. testUser
a) because in the second test method we see ```Sanctum``` and static call for ```actingAs``` it can be concluded that user authentication should be perform using **api tokens**. So in the register (/create a new user) we should add token to the user using below:
```php
$user['token'] = $user->createToken('api-token')->plainTextToken;
```
b) again based on route address ('/api/user') that references to AuthController@user method. And again because a customized result has been return. We should create another api resource (this time **name**-resource) called UserResource (for the sake of consistancy and scalablity another Request type is created with the title of UserRequest for now it is empty and is not used but later it would be filled with validation rules, authorization, etc. )
* **Note** that we could use ```$request->user()``` as input of UserResource, but first I checked ```$request->user()->id``` with the database to add an extra layer of security. 
* Now is the time to commit the changes as below:

| git message    | what have i done |
|----------------|:-----------------|
| test passed: AuthControllerTest@testUser | Added: UserResource. Added: UserRequest. Changed: AuthController@register. Changed: AuthController@user | 