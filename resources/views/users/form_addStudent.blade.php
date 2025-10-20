<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<h2>Ajouter un etudiant</h2>
<form action="/save_student" method="POST">
    @csrf
    <label>Nom</label>
    <input type="text" name="student_name" required><br>
    <label>email</label>
    <input type="email" name="student_email" required><br>
    <label>Birthday</label>
    <input type="number" min="1" name="student_birthday"><br>

    <button type="submit">Add student</button>
</form>
</body>
</html>