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

## 1. AuthControllerTest

### 1.1. testRegister

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

### 1.2. testUser
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

* ### intermission
    Here is the point, I think, I should add a [todo.md](./todo.md) file, so I don't miss what should I do in next versions and If I have more time, whereto invest it. I included it in my version-controlling so other team members and I work together and make it work.

    | git message    | what have i done |
    |----------------|:-----------------|
    | Added: todo.md | Added: todo.md so we can cooperate, down the road | 

## 2. DriverControllerTest
here is why i choose to work with this test class. because according to documentations, A driver is a **sub-entity** of **user** and these two are very related together. 

* **Note** that it is a hirearchial entity so I suggest an interface called **IUser** is added to the struture, so using **strategy design pattern** we can create subclasses and other sibbling-classes easily and correctly.

### 2.1. testSignup

a) each driver has at least two extra properties related to his/ her auto/car (```car_plate```, ```car_model```). and one extra property for the ```status```, that is modeled as Enumerator in **app/Enums/DriverStatus.php**. Using the data, we should again add a new resource: ```DriverResource```. It also noted an specified request class exists in my code base called ```DriverSignupRequest``` that handles validation of driver signup proccess, so I typehint it in the ```signup``` method. In this step i don't create a ```DriverSignupResource``` maybe later i add it if is needed.
b) we should check for 2 things in driver ```signup``` method
- Check if user is signed in
- If user is a driver already or not
    - for **optimization purposes**, to check a driver, it is better first to check if a user is not a driver because usually this type of requests is more recieved from normal users.
* Now is the time to commit the changes as below:

| git message    | what have i done |
|----------------|:-----------------|
| test passed: DriverControllerTest@testSignup | Added: DriverResource. Changed: todo.md. Changed: DriverController@signup. Changed:AuthController@user|


### 2.2. testUpdate
*[**DEPRICATED NOTES**: In this stage, because Travel controller is not defined, I omit the test for now, and work on passing Travel and TravelEvent unit tests.]*
* **Note**: Several assertion in one test method is not a good practice.

Based on the test below states can be inferred:
- if a request of put type goes into **/api/driver** with the **new data** of a driver it should update the driver and returns updated driver.
- it also reveals that only the drivers can update their own data.
- there are 3 travel, 2 of them are pending and driver can pick them. 
- the pending travels are the same as factory is defined. 

| git message    | what have i done |
|----------------|:-----------------|
| test passed: DriverControllerTest@testUpdate | Changed: DriverController@signup. Changed:DriverControllerTest@testUpdate|



## 3. TravelTest
The only TravelTest method is testRelations. Which the other team members have done the job by creating a greate Travel eloquent model. I did nothing here so, there would be no version controlling here. 

## 4. TravelEventTest.php
Again there is one and only test of this class is testRelations, which is passed because of a perfect TravelEvent elqouent model.
I think more test can be designed in 3 and 4 cases.

## 5. TravelControllerTest
In order to complete **TravelController** i ommit 2.2. tests for now.

### 5.1 testStore
testStore targets **TravelController@store** method. 
- There is a TravelStoreRequest class to handle these types of request, so I typehint it in the store methode. 
- Reverse engineering shows the authenticated passenger can create the travel, I get it from ```$request->user()```.
- TravelResource can not be used for store method output. So TravelStoreResource is devised to format the output.
- Spots are given by $request, if not fill them with empty array for furthur inspections.
- It should be checked if current user (passanger) can take a new travel (he/she has already taken a travel). If so an exception of type **ActiveTravelException** should be thrown. Because usually users act normally, this exception should be thrown after checking that the user didn't act normally (**optimization**)
- Because each travel at least consists of two spots, and these spots should be store inside database and also other information of travel should be stored seperately, using **transaction** is highly recommanded. 
- the status of the user should be **SEARCHING_FOR_DRIVER** at this initial stage.
- the f...ing ```status``` should be added to fillable property of the Travel model. So i can use mass assignment. (I could use save method but that is slower than create static call and that's not my style.) Also ```passenger_id``` is added to the fillable.
- ```position, latitude, longitude``` should be added to fillables of **TravelSpot** model. (Only to save some millisecods, and also in compliance of my codding style)
* Now is the time to commit the changes as below:
    
| git message    | what have i done |
|----------------|:-----------------|
| test passed: TravelControllerTest@testStore | Added: TravelStoreResource. Changed: Travel. Changed: TravelController. Changed:TravelSpot. Changed: TravelControllerTest|

### 5.2 testStoreWithBadPositions, testStoreWhenHaveActiveTravel
these two tests are also passed using provided code in 5.1. so no need to version-control (no changes !)

### 5.3 testCancelSearchingForDriverAsPassenger
this test targets **TravelController@cancel** in route */travels/{travel}/cancel*. 
- based on the test, a logged-in user (passanger of a travel) can cancel the travel, so we need to extract passanger info. 
- based on the test, and also the api call, data of the travel (travel_id) should be inserted into the method as args.
- Travel should be exsited and assigned to the passange in order to let him/her to cancel it. 
    * using {travel} as arg name, is a bad naming decision. I think it is better to use {id} or {travel_id} instead. 
- Finished or already canceled travels should not be cancelable. In the **TravelStatus** point of view, ```DONE``` and ```CANCELLED``` are not cancellable. 
    * **optimization** despite pervious situations, this time it is better to put abnormal users' logic first, because it is possible to that other statuses be added in the TravelStatus enums.
- because there is a user defined exception called ```CannotCancelFinishedTravelException``` for one mentioned situations, i used the exception for both of cases, but it is better to define specific exceptions for either of cases. 
- user also cannot cancel already started travel. there is another exception class for that too. ```CannotCancelRunningTravelException```
    * **optimization** there is no need to add it in ```elseif``` block, since previous ```if``` block is used to throw exceptions. 
- if non of mentioned situation is the case, passange may cancel the travel, and travel's status updated as canceled.
- note that, up to this stage the one other test is passed correctly **testCancelFinishedTravel**
* Now is the time to commit the changes as below:
    
| git message    | what have i done |
|----------------|:-----------------|
| test passed: TravelControllerTest@testCancelSearchingForDriverAsPassenger and testCancelFinishedTravel | Changed: todo.md. Changed: TravelControllerTest. Changed: TravelController|


### 5.4 testCancelOnboardPassenger
- First of all it is a bad naming decision to use similar names instead of exact naming. There is a method called ```passengerIsInCar``` but here the test specify to test **OnBoard**. so it is better to change method name because OnBoard is more general and can model future needs.
- To pass this test we should modify the cancel method so it models both passengers and drivers. for instance, $passanger subtitutes with $user, and where clause changes to 
```php
...->where(function ($query) use ($user) {
    $query->where('passenger_id', '=', $user->id)->orWhere('driver_id', '=', $user->id);
})
```
* Now is the time to commit the changes as below:
    
| git message    | what have i done |
|----------------|:-----------------|
| test passed: TravelControllerTest@testCancelOnboardPassenger | Changed: todo.md. Changed: TravelControllerTest. Changed: TravelController|

### 5.5. testCancelArrivedCar
As it is stated in TestingTravel concerns, **users can not cancel** an already running travel. Also based on the test method **drivers can cancel** an already running travel. This test should be considered for more improvements. 
* Now is the time to commit the changes as below:
    
| git message    | what have i done |
|----------------|:-----------------|
| test passed: TravelControllerTest@testCancelArrivedCar | Changed: todo.md. Changed: TravelControllerTest. Changed: TravelController|

### 5.6. testView
The test targets **TravelController@view** and implies that both passangers and drivers can view the travel and I think it is better for them to see their own travels not travels of the others. And because it is a get request, it is better to use request() global helper instead of injecting $request into the method.
* Now is the time to commit the changes as below:
    
| git message    | what have i done |
|----------------|:-----------------|
| test passed: TravelControllerTest@testView | Changed: TravelControllerTest. Changed: TravelController|

### 5.7. testPassengerOnBoard
The test targets **TravelController@passengerOnBoard** which is a post request in the api. And checks if a passanger is on board in any of travel events. 

- it checks driver's passanger (the passanger of the driver), so **the driver can check this endpoint**.
- it should return the travel with its associated events. 
- to create TravelEvent in my way $fillable property is defined as below:
    ```php
    protected $fillable = ['travel_id', 'type'];
    ```
- if there are no passangers on board, user (driver) can not check for it (there is no action for the user here) InvalidTravelStatusForThisAction
- **very important note** here either the factory is wrong or the test should not assertTrue. I think the first scenario is the case. Because I checked the factories of travel (TravelFactory) and travel events (TravelEventFactory), and none try to make a travel with travel event status of **PASSENGER_ONBOARD**, so 
    - I modify the **TestingTravel** file and add ```runningTravelPassangerOnBoard``` method in it.
    - changed ```acceptByDriver``` with ```passengerOnBoard```
    - Update the test case from ```runningTravel``` to ```runningTravelPassangerOnBoard```
    - add another test case for the case that user is not on board in the travel using ```runningTravel```.
    - I added another header in the **todo.md** and report this for furthur discussion. 
* Now is the time to commit the changes as below:
    
| git message    | what have i done |
|----------------|:-----------------|
| test passed: TravelControllerTest@testPassengerOnBoard | Changed: todo.md. Changed: TravelControllerTest. Changed: TravelController. Changed: TestingTravel. Changed: TravelEvent.|

### 5.8. testPassengerOnBoardAsPassenger
this test is already passed bacause of the logic of previous test and reverse-engineering. so no need of version-controlling here. 

###5.7. testPassengerOnBoardWhenCarIsNotArrived
the test like its predecessors targets **TravelController@passengerOnBoard**. Note that if we check if ```driverHasArrivedToOrigin``` **before** ```passengerIsInCar``` the logic of the program would be in compliance with the test cases otherwise it is not the case.
* Now is the time to commit the changes as below:
    
| git message    | what have i done |
|----------------|:-----------------|
| test passed: TravelControllerTest@testPassengerOnBoardWhenCarIsNotArrived and testPassengerOnBoardAsPassenger | Changed: TravelControllerTest. Changed: TravelController.|

### 5.9. testPassengerOnBoardFinishedTravel
If the travel is finished, no one allowed to check if somebody is on board. **IS IT LOGICALLY A GOOD ASSUMPTION ?**

| git message    | what have i done |
|----------------|:-----------------|
| finished: all tests of passengerOnBoard method is finished. test passed: TravelControllerTest@testPassengerOnBoardFinishedTravel | Changed: TravelControllerTest. Changed: TravelController. Changed: todo.md|

### 5.10. testDone
It targets **/travels/{travel}/done** of api routes and **TravelController@done()** method in the controller. **several matters are tested in this single method, that may not be a good Idea**. 
Based on the test setup and the first assertion:
 - driver can mark a travel as done. 
 - because the end of the travel should have effects in both travel and travel event entities, using **trasaction** is advised. 
Based on the second assertion:
 - when a travel is done, as mentioned earlier, a travel event entity with the status of DONE should be created. (done in previous assertion)
Based on the third assertion:
 - it should be checked if the travel is not in ```DONE, SEARCHING_FOR_DRIVER, CANCELLED``` states. The only status that can be transformed to ```DONE``` is ```RUNNING```.
* Now is the time to commit the changes as below:
    
| git message    | what have i done |
|----------------|:-----------------|
| test passed: TravelControllerTest@testDone | Changed: TravelControllerTest. Changed: TravelController. Changed: todo.md|

###5.10. testDoneAsPassenger
based on this test in contrast with the driver, the user can not mark a travel as done. This situation was implied based on pervious test, so no need for version-control, here. 
**BUT NOTE THAT** in cancel method both of the passanger and driver could mark a travel as canceled !

### 5.11. testDoneWhenSpotsSkipped
using provided eloquent-model method ```allSpotsPassed``` in the **Travel** class, we can check if all spotes are passed or not. If the latest is the case, driver is not able to mark the travel as done. Because this case has its own exception, i put it in a seperate ```if``` statement block, and of course before i check travel is in ```RUNNING``` state.
* Now is the time to commit the changes as below:
    
| git message    | what have i done |
|----------------|:-----------------|
| test passed: TravelControllerTest@testDoneWhenSpotsSkipped | Changed: TravelControllerTest. Changed: TravelController. Changed: todo.md|

### 5.12. testTake
This test and multiple later ones, target **TravelControl@take** and try to model if a driver can or can not take a travel. based on the current test:
 - driver can take travel with the status of ```SEARCHING_FOR_DRIVER```
 - the weird thing here is that based on the needed results, after trying to take the travel, driver_id is updated but the status remains ```SEARCHING_FOR_DRIVER```. I noted this issue in the todo.md under concerns. 
 - ?????????????????? **VERY IMPORTANT** thing to note here is that maybe there are several drivers who try to take on single travel, so the travel should be **LOCKED PESSIMISTICALLY** ?????????????????? . I noted this in the concerns. Later I will adderess the issue using **transactions**, ```lockForUpdate()``` and other tools and techniques. But for now i just code it fast and lazy :D

* Now is the time to commit the changes as below:
    
| git message    | what have i done |
|----------------|:-----------------|
| test passed: TravelControllerTest@testTake | Added: TravelTakeResource. Changed: TravelControllerTest. Changed: TravelController. Changed: todo.md.|


### 5.13. testTakeCancelledTravel
based on this test driver can not take a cancelled travel. otherwise a specialized exception will be thrown (```InvalidTravelStatusForThisActionException```). 
**optimization**: Because normal people don't send this kind of requests, i code it after 
```php
if($theTravel->status == TravelStatus::SEARCHING_FOR_DRIVER)
```
* Now is the time to commit the changes as below:
    
| git message    | what have i done |
|----------------|:-----------------|
| test passed: TravelControllerTest@testTakeCancelledTravel | Changed: TravelControllerTest. Changed: TravelController. |

### 5.14. testTakeWithActiveTravel
This test aims to prevent drivers who already are in a travel. 
- **Optimization**: because the problem with multiple travels occurs to the drivers, so before even we recreate the travel from provided id, we can check this fact. but because we are going to use a static method in the **Travel** class instead of **User** or **Driver** model classes, again i suggest impelemting the check inside of **Driver** class. also using strategy DP is suggested to model users, drivers, and passangers.
- we can check that the driver has active travels using ```userHasActiveTravel``` method. 

* Now is the time to commit the changes as below:
    
| git message    | what have i done |
|----------------|:-----------------|
| finished: all tests of TravelController are passed.  test passed: TravelControllerTest@testTakeWithActiveTravel | Changed: TravelControllerTest. Changed: TravelController. |

*at this stage i will finish the DriverControllerTest but it is documented in section 2.2. look at the git changes to know more.

## 6. TravelSpotControllerTest
All tests in this section targets travel's spot entity in **TravelSpotController**

### 6.1. testArrived()
Below are the assumptions that can be extracted from the test:
    - first assertion:
        - **TravelSpotController@arrived** method is targeted.
        - drivers can get the travel spot info.
        - spot id and travel id should be added as get parameters to the ```arrived``` method.
        - response should be loaded with travel's spots. that is provided before by **TravelResource** 
    - second assertsion:
        - the travel should include an origin spot (position 0)
        - the spot arrived_at should be filled by current time stamp. (any time but logically current time is the correct time.)
    - third assertion:
        - only drivers can mark travel as arrived. 

* Now is the time to commit the changes as below:
    
| git message    | what have i done |
|----------------|:-----------------|
| test passed: TravelSpotControllerTest@testArrived | Changed: TravelSpotControllerTest. Changed: TravelSpotController. |

### 6.2. testArrivedAsPassenger()
the whole test is similar to the third assertion of previous test:
```php
Sanctum::actingAs($passenger);
$response = $this->postJson("/api/travels/{$travel->id}/spots/{$origin->id}/arrived")
    ->assertStatus(403);
```
and it is passed already. So no need for version-controlling.


### 6.3. testArrivedNotRunningTravel()
Again it targets and from the test below could be understood:
    - travel with status of CANCELLED is feeded to the api
    - drivers may check the endpoint
    - If travel is cancelled an exception should be thrown from ```InvalidTravelStatusForThisActionException``` exception class.
    - the check should be perform after recreating the travel.
* Now is the time to commit the changes as below:
    
| git message    | what have i done |
|----------------|:-----------------|
| test passed: TravelSpotControllerTest@testArrivedNotRunningTravel and  TravelSpotControllerTest@testArrivedAsPassenger | Changed: TravelSpotControllerTest. Changed: TravelSpotController. |

### 6.4. testArrivedWhenAlreadyArrived()
From the test can be found that:
    - A running travel is feeded to the api
    - the driver already has been arrived to the origin (first spot == position 0)
    - in the tested situation an exception of type **SpotAlreadyPassedException** should be thrown.
    - after recreating the travel the check could be performed.

**To better model the pervious test**,  i changed the check form
```php 
if($theTravel->status == TravelStatus::CANCELLED)
```
to 
```php
if($theTravel->status != TravelStatus::RUNNING)
```

* Now is the time to commit the changes as below:
    
| git message    | what have i done |
|----------------|:-----------------|
| test passed: TravelSpotControllerTest@testArrivedWhenAlreadyArrived | Changed: TravelSpotControllerTest. Changed: TravelSpotController. |

### 6.5. testStore()
It tragets, **TravelSpotController@store** using post method.
    - the ```assertPositionsInRange``` should be uncommented. 
    - In addition to ```Request $request``` travel_id also should be added in args of ```store``` method.
    - passanger only can store/ create a travel spot. 
    - to store travel spots there is a specialized class: **TravelSpotStoreRequest** so instead of **Request** this would be injected. 
    - the first assertion implies that a user can store a travel spot with the **position of 1 twice**, but that is not the case for the second and the third one. I added this issue in my concerns in todo.md. And I changed ```$spot['position'] == 1``` to ```$spot['position'] == 2```. I also changed the controller to fix the issue. 

* Now is the time to commit the changes as below:
    
| git message    | what have i done |
|----------------|:-----------------|
| test passed: TravelSpotControllerTest@testStore | Changed: TravelSpotControllerTest. Changed: TravelSpotController. Changed: todo.md. Changed: TravelSpot|

### 6.6. testStoreAsDriver()
I passed this test before, so no need for version control.


###6.6. testStoreOutOfRange
This test says user can not ask to insert a spot with the position more than ```latestPositionOfTravel + 1 ``` otherwise an error should be returned with the status code of 422. 

* Now is the time to commit the changes as below:
    
| git message    | what have i done |
|----------------|:-----------------|
| test passed: TravelSpotControllerTest@testStoreOutOfRange | Changed: TravelSpotControllerTest. Changed: TravelSpotController.|

### 6.7. testStoreArrived
user can not store a new spot if he/she already arrived there. so below will do the job:
```php
if ($theTravel->allSpotsPassed()) {
    throw new SpotAlreadyPassedException();
}
```
| git message    | what have i done |
|----------------|:-----------------|
| test passed: TravelSpotControllerTest@testStoreArrived | Changed: TravelSpotControllerTest. Changed: TravelSpotController.|

### 6.8. testStoreNotRunningTravel
If it is not a travel with ```RUNNING``` status, so passanger is unable to add spots to it. 

| git message    | what have i done |
|----------------|:-----------------|
| test passed: TravelSpotControllerTest@testStoreNotRunningTravel | Changed: TravelSpotControllerTest. Changed: TravelSpotController.|

### 6.9. testDestroy
from now on, all test target **/travels/{travel}/spots/{spot}** using delete method. the method needs ```travel_id``` and ```spot_id``` to proccess requests. 
    - the spot with provided ```travel_id``` and ```id``` should be delete.
    - other later points should be decremented by one. below makes it work:
```php
TravelSpot::where([['travel_id', '=', $travel],['id', '>', $spot]])->decrement('position');
```
| git message    | what have i done |
|----------------|:-----------------|
| test passed: TravelSpotControllerTest@testDestroy | Changed: TravelSpotControllerTest. Changed: TravelSpotController.|

###6.8. testDestroyNotRunningTravel
when a travel is in ```RUNNING``` status, do deletion is allowed. 

| git message    | what have i done |
|----------------|:-----------------|
| test passed: TravelSpotControllerTest@testDestroyNotRunningTravel | Changed: TravelSpotControllerTest. Changed: TravelSpotController.|

### 6.10. testDestroyNotRunningTravel
This test already is checked and passed. 

### 6.11. testDestroyArrived
This test is similar to the one for store and passed easily, using the same check:
```php
if ($theTravel->allSpotsPassed())
	throw new SpotAlreadyPassedException();
```

| git message    | what have i done |
|----------------|:-----------------|
| test passed: TravelSpotControllerTest@testDestroyArrived | Changed: TravelSpotControllerTest. Changed: TravelSpotController.|


### 6.12. testDestroyOrigin
Passanger can not delete the origin (first spot).
| git message    | what have i done |
|----------------|:-----------------|
| test passed: TravelSpotControllerTest@testDestroyOrigin | Changed: TravelSpotControllerTest. Changed: TravelSpotController.|

### 6.13. testDestroyLastSpot
Passanger can not delete the last spot. the fully fledged piece of code that checks two latest tests for (Origin/ Destination) spots is as follow:
```php
if ($theSpot->position == 0 || $theSpot->position == $theTravel->spots->max('position'))
	throw new ProtectedSpotException();
```
| git message    | what have i done |
|----------------|:-----------------|
| test passed: TravelSpotControllerTest@testDestroyLastSpot | Changed: TravelSpotControllerTest. Changed: TravelSpotController.|

# E. FINAL WORDS
That is about it. There are some concerns in my implementation that i stated in the todo.md. Also there are lots fields of imporvment like: 

- DRY-ing my code, 
- Using some service providers, 
- Using a lot of repositories and interfaces.
- Using code documenting tools like phpDocs. 

Please note that my coding style differs based on **codebase**, **project requirments**, and **available time-budget**. Here I present my thinking style when I try to **turn designed tests to the program logic**, or better said, my **reverse-engineering skills**. Also it presents my sweet **addiction for documenting** my work. But it is not presenting my problem solving skills, my passion to work as a team memeber, and my love to discuss and cooperate in problem solving sessions.
At the end i want to thank all, who read through this very long verbose load-thinking journal. 
I also added a README.md file at the root directory of my project that presents the required results and reports. 
**Regards and peace out everybody**, ```Ali``` :D
