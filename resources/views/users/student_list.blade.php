<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<table border="1" cellpadding="10" cellspacing="0">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Birthday</th>
        </tr> 
    </thead> 
    <tbody>
        @foreach($students as $student)
        <tr>
           <td>{{$student->student_name}}</td>
           <td>{{$student->student_email}}</td>
            <td>{{$student->student_birthday}}</td>
        </tr>
        @endforeach
    </tbody>
</table>      
</body>
</html>