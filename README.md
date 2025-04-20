 Example Usage
Create a Patient
Read All Patients
Update a Patient
Delete a Patient
SQLite Database Schema
The script automatically creates the following table if it doesn't exist:

curl -X POST -H "Content-Type: application/json" -d '{"name":"John Doe","age":30,"gender":"Male","address":"123 Main St","phone":"1234567890"}' http://localhost:8000/api.php?action=create

curl http://localhost:8000/api.php?action=read

curl -X PUT -H "Content-Type: application/json" -d '{"name":"Jane Doe","age":28,"gender":"Female","address":"456 Elm St","phone":"9876543210"}' http://localhost:8000/api.php?action=update&id=1

curl -X DELETE http://localhost:8000/api.php?action=delete&id=1



php -S localhost:8000