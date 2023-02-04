- # CONCERNS (NEED FOR QUESTIONS AND DISCUSSIONS)
    - In **TravelControllerTest@testPassengerOnBoard** I changed the logic of the test, I really need some discussions here. I couldn't reverse-engineer the case (please help, everyone !)
    - In **testPassengerOnBoardFinishedTravel** test case, is it a good practice to prevent users to check for onboard passangers, if it is already finished ?
- # TO DO (MUST)
    - **Add: ```setPasswordAttribute```** method in User class, so the password be mutated without doning anything in the controller
    - **Check** for **authorization** issues through the project. Like when a driver tries to check for onboard passangers, he/she only is authorized to check it in his/her own travels, not somebody's else. 
    - **Substitude** request typehints ```Request $request``` with more specialized Request classes. I mean create a request class for each method call in the controller. [for now, just finish the job !]
    - I repeated myself in several cases through the codding proccess like:
    ```php
    $theTravel = Travel::where([['id', '=', $travel], ['driver_id', '=', $driver->id]])->with(['events'])->firstOrFail();
    ```
    or
    ```php
    if(!Driver::isDriver($driver) ) return abort(403);
    ```
    so, after finishing the job, use repository dp and some service providers and make the program more DRY !

- # IDEAS (SHOULD)
    - **Use: Strategy DP**, for User related classes like, users, drivers, travelers, personals, etc. Try to use **composition instead of inheritance**.
    - **Seperate: entity access** concerns using different kinds of **repositories** and **service providers**
    - using **{travel}** as arg name, is a bad naming decision. I think it is better to use **{id}** or **{travel_id}** instead. 
    - there is ```CannotCancelFinishedTravelException``` exeption but another one is needed for already canceled travels like ```CannotCancelAlreadyCancelledTravelException```
    - use better naming convensions and follow them stricktly. for example use ```passengerIsOnBoard``` instead of ```passengerIsInCar```.
    - It is better to use lot less assetions in each test case (test method) than what it is in current code base. 

- # FURTHER STUDIES (NOTE)
    - Write a better ```TravelControllerTest@testCancelArrivedCar``` test.

