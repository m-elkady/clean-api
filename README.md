# Jobleads fullstack Task

### Project setup

#### Backend

* Add `jobleads.localhost` to your `/etc/hosts`: `127.0.0.1 jobleads.localhost`

* Run `make init` to initialize project

* Open in api platform tool (POSTMAN): http://jobleads.localhost/user

* to consume the user api resource follow the instructions: 

### endpoints

- #### User

    - ##### create

        - to create new user into our database
        ```
        POST /user Contet-Type=application/json
        {
            "first_name": "User First name",
            "last_name": "User Last name",
            "user_email": "email@test.com"
        }
        ```
    
    - ##### Get user

        - to read a user data from our database
        - the default value for fieldName is id so you can consume the endpoint as fowllows: `/user/{id}` or `/user/User Last name/last_name`
        ```
        GET /user/{value}/{fieldName}
        ```

    - ##### update

        - to update a user data
        ```
        PUT /user/{id} Contet-Type=application/json
        {
            "first_name": "new User First name",
            "last_name": "new User Last name",
            "user_email": "email_new@test.com"
        }
        ```
    
    - ##### Delete

        - to delete a user record 
        ```
        DELETE /user/{id}
        ```
    
    - ##### List (Paginate)

        - to paginate All users by parameters (perPage, sortBy, order)  
            ```
            GET /user/?perPage=10&sortBy=firstName&order=desc
            ```
        - to search by criteria (firstName, lastName, userEmail)
            ```
            GET /user/?perPage=10&sortBy=firstName&order=desc&firstName=First name
            ```

#### Frontend

* Open in your browser: http://localhost:3000/