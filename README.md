
#API Endpoints

Initialize super admin:

<img width="1240" alt="Screenshot 2024-12-15 at 11 37 04" src="https://github.com/user-attachments/assets/b940297c-48d0-4b44-b9e5-75c8dc1b08d0" />

User
1. Get all users:
 Method:Get
 http://localhost:5051/api/users

Response:

<img width="797" alt="image" src="https://github.com/user-attachments/assets/840073f3-4c25-42a7-abd8-19582772a73a" />



2. Login:
 Method:Post 
http://localhost:5051/api/users/login

   Response:

<img width="839" alt="image" src="https://github.com/user-attachments/assets/19e8503e-283b-4037-b73e-765621708ff9" />



3. Register:
 Method: Post
 http://localhost:5051/api/users/register

   Response:

   <img width="512" alt="image" src="https://github.com/user-attachments/assets/c89cf96f-b52b-4d9f-8f10-98b5e390937f" />



4. View profile :
 Method: Get
 http://localhost:5051/api/users/profile

   - requires authentication

   Response:

   <img width="709" alt="image" src="https://github.com/user-attachments/assets/a7a451d9-15d6-425c-9003-6b34f1c726cb" />



5. Edit profile:
 Method:Post 
http://localhost:5051/api/users/profile

   - requires authentication

   Response

   <img width="783" alt="image" src="https://github.com/user-attachments/assets/df9e27f6-7fab-4354-a5d2-272ceb6d7bc3" />



Post
1. Create post
 Method: Post
 http://localhost:5051/api/posts/

    - requires authentication

   Response:

<img width="572" alt="image" src="https://github.com/user-attachments/assets/4b9f06b8-50ad-4099-a168-398a4162d641" />


2. Get all posts: Method Get http://localhost:5051/api/posts/

 - requires authentication

   Response:

   <img width="526" alt="image" src="https://github.com/user-attachments/assets/90bf3aa9-b65a-46d6-8569-f9dc3344ef5b" />


3. Get specific post : Method: Get http://localhost:5051/api/posts/{id}

   - requires authentication

   Response :

  <img width="451" alt="image" src="https://github.com/user-attachments/assets/1b13ccea-d6fa-49a2-a8ed-afb862242143" />


4. Update post : Method: Patch http://localhost:5051/api/posts/{id}

   - requires admin

   <img width="363" alt="image" src="https://github.com/user-attachments/assets/d6ffa6a7-2a76-4669-87ef-4282c1fe68f3" />


5. Delete post: Method: Delete http://localhost:5051/api/posts/{id}

  - requires admin

    <img width="293" alt="image" src="https://github.com/user-attachments/assets/24b7e677-7904-4b2e-b22a-30f20172b7d4" />


Comments
1. Get all comments of specific post : Method:Get http://localhost:5051/api/posts/{id}/comments

   - requires authentication

   Response:

   <img width="470" alt="image" src="https://github.com/user-attachments/assets/837df6ab-af38-4b21-90be-288fa84d5f7a" />


2. Post a comment to a specific post: Method:Post http://localhost:5051/api/posts/{id}/comments

   - requires authentication

     Response:

   <img width="255" alt="image" src="https://github.com/user-attachments/assets/7b71b8ee-8e66-4093-a296-bf277268c8ca" />


Images

1. Create an image associated to post: Method: Post http://localhost:5051/api/posts/{postId}/images

  - requires authentication

    Response

  <img width="690" alt="image" src="https://github.com/user-attachments/assets/0ee7941e-fea4-4832-b708-fe829816badb" />


2. Get all images associated to post: Method: Get http://localhost:5051/api/posts/{postId}/images  

  -requires authentication

  Response

  <img width="428" alt="image" src="https://github.com/user-attachments/assets/51f9030a-23b0-4356-a4e6-cade3bed0544" />

 
3. Delete an image associated to a post: Method: Delete  http://localhost:5051/api/posts/{postId}/images/{fileId}

  -requires authentication

  Response

  <img width="455" alt="image" src="https://github.com/user-attachments/assets/738db3cf-54a6-4307-9770-ddf192b894fd" />







   

