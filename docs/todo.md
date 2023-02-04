- # TO DO (MUST)
    - **Add: ```setPasswordAttribute```** method in User class, so the password be mutated without doning anything in the controller

- # IDEAS (SHOULD)
    - **Use: Strategy DP**, for User related classes like, users, drivers, travelers, personals, etc. Try to use **composition instead of inheritance**.
    - **Seperate: entity access** concerns using different kinds of **repositories** and **service providers**
    - using **{travel}** as arg name, is a bad naming decision. I think it is better to use **{id}** or **{travel_id}** instead. 
    - there is ```CannotCancelFinishedTravelException``` exeption but another one is needed for already canceled travels like ```CannotCancelAlreadyCancelledTravelException```
    - use better naming convensions and follow them stricktly. for example use ```passengerIsOnBoard``` instead of ```passengerIsInCar```.

- # FURTHER STUDIES (NOTE)

