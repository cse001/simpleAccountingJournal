<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Styled Radio Buttons</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        form {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        p {
            font-size: 18px;
            margin-bottom: 10px;
        }

        input[type="radio"] {
            accent-color: #007BFF; /* Blue color for radio buttons */
            width: 20px;
            height: 20px;
            margin-right: 10px;
            cursor: pointer;
        }

        label {
            font-size: 16px;
            cursor: pointer;
            margin-bottom: 8px;
            display: inline-block;
        }

        button {
            margin-top: 15px;
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <form>
        <p>Choose your favorite programming language:</p>
        
        <input type="radio" id="python" name="language" value="Python">
        <label for="python">Python</label><br>

        <input type="radio" id="javascript" name="language" value="JavaScript">
        <label for="javascript">JavaScript</label><br>

        <input type="radio" id="csharp" name="language" value="C#">
        <label for="csharp">C#</label><br>

        <input type="radio" id="java" name="language" value="Java">
        <label for="java">Java</label><br>

        <button type="submit">Submit</button>
    </form>
</body>
</html>
