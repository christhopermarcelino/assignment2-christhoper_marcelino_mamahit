<!-- Christhoper Marcelino Mamahit -->
<!-- BFLP IT 24 -->
 
 <?php

session_start();

include("database.php");

$conn = getDBConnection();
if(!$conn) {
    $_SESSION['alert-failed'] = "Terjadi kesalahan sistem. Koneksi database gagal";
}

$validations = [
    "name" => function ($value) {
        if (empty($value)) {
            $_SESSION['error']['name'][] = "Nama tidak boleh kosong";
        }
        if (strlen($value) < 6) {
            $_SESSION['error']['name'][] = "Nama minimal 6 karakter";
        }
    },
    "role"  => function ($value) {
        if (empty($value)) {
            $_SESSION['error']['role'][] = "Role tidak boleh kosong";
        }
    },
    "availability"  => function ($value) {
        if (empty($value)) {
            $_SESSION['error']['availability'][] = "Status Pekerjaan tidak boleh kosong";
        }
    },
    "age"  => function ($value) {
        if (empty($value)) {
            $_SESSION['error']['age'][] = "Usia tidak boleh kosong";
        }
        if ($value <= 0) {
            $_SESSION['error']['age'][] = "Usia tidak bisa nol atau negatif";
        }
        if (!filter_var($value, FILTER_VALIDATE_INT)) {
            $_SESSION['error']['age'][] = "Usia harus berupa angka bukan desimal";
        }
    },
    "location"  => function ($value) {
        if (empty($value)) {
            $_SESSION['error']['location'][] = "Lokasi tidak boleh kosong";
        }
    },
    "years_experience"  => function ($value) {
        if (empty($value)) {
            $_SESSION['error']['years_experience'][] = "Lama pengalaman kerja tidak boleh kosong";
        }
        if ($value < 0) {
            $_SESSION['error']['years_experience'][] = "Lama pengalaman kerja tidak bisa negatif";
        }
        if (!filter_var($value, FILTER_VALIDATE_INT)) {
            $_SESSION['error']['years_experience'][] = "Lama pengalaman kerja harus berupa angka bukan desimal";
        }
    },
    "email" => function ($value) {
        if (empty($value)) {
            $_SESSION['error']['email'][] = "Email tidak boleh kosong";
        }
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error']['email'][] = "Format email tidak sesuai";
        }
    }
];

function checkDBStatus() {
    global $conn;

    if(!$conn) {
        throw new Exception("Terjadi kesalahan sistem. Koneksi database gagal");
    }
}

function getPostData($field)
{
    return isset($_POST[$field]) ? htmlspecialchars($_POST[$field]) : '-';
}

function validateData($data)
{
    global $validations;

    foreach ($validations as $val_name => $val_function) {
        $val_function($data[$val_name]);
    }

    if(!empty($_SESSION['error'])) {
        $_SESSION['error_exists'] = true;
    }
}

function insertData($data)
{
    global $conn;

    try {
        checkDBStatus();

        $sql = $conn->prepare(
            "INSERT INTO `users` (`name`, `role`, `availability`, age, `location`, years_experience, email) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
    
        $sql->bind_param("sssisis", $data["name"], $data["role"], $data["availability"], $data["age"], $data["location"], $data["years_experience"], $data["email"]);
    
        $result = $sql->execute();
        if ($result) {
            $_SESSION['alert-success'] = "Data berhasil dibuat";
        } else {
            $_SESSION['alert-failed'] = "Data gagal dibuat. Silakan coba lagi";
        }
    
        header("location: index.php?id=$sql->insert_id");
    } catch(Exception $_) {
        $_SESSION['data'] = $data;
        $_SESSION['alert-failed'] = "Data gagal dibuat. Silakan coba lagi";
        header("Location: " . $_SERVER["HTTP_REFERER"]);
    } finally {
        unset($_SESSION["error"]);
    }
}

function getData($id)
{
    global $conn;

    try {
        checkDBStatus();

        $sql = "SELECT * FROM `users` WHERE id = $id";
        $result = mysqli_query($conn, $sql);
        if ($result->num_rows == 0) {
            $_SESSION['error-not-found'] = "Data tidak ditemukan";
            return;
        }
    
        $result = $result->fetch_assoc();
        global $data;

        $data['name'] = $result['name'] ?? '-';
        $data['role'] = $result['role'] ?? '-';
        $data['availability'] = $result['availability'] ?? '-';
        $data['age'] = $result['age'] ?? '-';
        $data['location'] = $result['location'] ?? '-';
        $data['years_experience'] = $result['years_experience'] ?? '-';
        $data['email'] = $result['email'] ?? '-';
    } catch(Exception $_) {
        $_SESSION['alert-failed'] = "Data gagal dibuat. Silakan coba lagi";
    }
}

switch ($_SERVER["REQUEST_METHOD"]) {
    case 'GET':
        $id = $_GET['id'] ?? null;
        if ($id) {
            getData($id);
        }
        break;
    case "POST":
        $insertData = [
            "name" => getPostData("name"),
            "role" => getPostData("role"),
            "availability" => getPostData("availability"),
            "age" => getPostData("age"),
            "location" => getPostData("location"),
            "years_experience" => getPostData("years_experience"),
            "email" => getPostData("email")
        ];

        validateData($insertData);
        if (!empty($_SESSION['error'])) {
            $_SESSION['data'] = $insertData;
            header("Location: " . $_SERVER["HTTP_REFERER"]);
            return;
        }
        insertData($insertData);
        break;
}
