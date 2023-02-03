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
    | Added: docker containers and needed ```.env``` fields     | in this step multiple directories, docker-compose.yml and assocciated **Dockerfile**s are added to project and the project is ready to be ran|

# C. Migrate Data, To DBMS
