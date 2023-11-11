<?php
session_start();
class database
{
    private $host = "mysql";
    private $db_name = "yavuzlar";
    private $username = "root";
    private $password = "secret";
    public $conn;
    public function getConnection()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}


class users
{

    public $id;
    public $name;
    public $surname;
    public $username;
    public $password;
    public $role;
    public $created_at;

    public function __construct($id, $name, $surname, $username, $password, $role, $created_at)
    {
        $this->id = $id;
        $this->name = $name;
        $this->surname = $surname;
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
        $this->created_at = $created_at;
    }
}

class role
{
    public $admin;
    public $teacher;

    public $student;

    public function __construct($admin, $teacher, $student)
    {
        $this->admin = $admin;
        $this->teacher = $teacher;
        $this->student = $student;
    }
}

class userController
{
    public database $db;
    public function __construct()
    {
        $this->db = new database();
    }


    public function adminControl()
    {
        if (isset($_SESSION["user"])) {
            if ($_SESSION["user"]["role"] != "admin") {
                header("Location: index.php");
                exit;
            }
        } else {
            header("Location: index.php");
            exit;
        }
    }

    public function teacherControl()
    {
        if (isset($_SESSION["user"])) {
            if ($_SESSION["user"]["role"] != "teacher") {
                header("Location: index.php");
                exit;
            }
        } else {
            header("Location: index.php");
            exit;
        }
    }

    public function studentControl()
    {
        if (isset($_SESSION["user"])) {
            if ($_SESSION["user"]["role"] != "student") {
                header("Location: index.php");
                exit;
            }
        } else {
            header("Location: index.php");
            exit;
        }
    }




    public function login($username, $password)
    {
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            if (password_verify($password, $result["password"])) {
                $_SESSION["user"] = $result;
                if ($result["role"] == "admin") {
                    header("Location: adminpanel.php");
                } else if ($result["role"] == "teacher") {
                    header("Location: teacherpanel.php");
                } else if ($result["role"] == "student") {
                    header("Location: studentpanel.php");
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function logout()
    {
        session_destroy();
        header("Location: index.php");
        exit;
    }






    public function userAdd($name, $surname, $username, $password, $role)
    {
        $hashedPassword = password_hash($password, PASSWORD_ARGON2I);

        $query = "INSERT INTO users (name,surname,username,password,role) VALUES (:name,:surname,:username,:password,:role)";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":surname", $surname);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $hashedPassword);
        $stmt->bindParam(":role", $role);
        $result = $stmt->execute();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }






    public function getUserInfo()
    {
        $query = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(":id", $_SESSION["user"]["id"]);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $user = new users($result["id"], $result["name"], $result["surname"], $result["username"], $result["password"], $result["role"], $result["created_at"]);
        return $user;
    }

    public function getTeachers()
    {
        $query = "SELECT * FROM users WHERE role = 'teacher' AND id != 1";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $teachers = [];
        foreach ($result as $item) {
            $teacher = new users($item["id"], $item["name"], $item["surname"], $item["username"], $item["password"], $item["role"], $item["created_at"]);
            array_push($teachers, $teacher);
        }
        return $teachers;
    }

    public function getStudents()
    {
        $query = "SELECT * FROM users WHERE role = 'student' AND id != 1";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $students = [];
        foreach ($result as $item) {
            $student = new users($item["id"], $item["name"], $item["surname"], $item["username"], $item["password"], $item["role"], $item["created_at"]);
            array_push($students, $student);
        }
        return $students;
    }


    public function searchTeachers($searchInput)
    {
        $query = "SELECT * FROM users WHERE role = 'Teacher' AND (name LIKE :search OR surname LIKE :search OR username LIKE :search)";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindValue(':search', '%' . $searchInput . '%', PDO::PARAM_STR);
        $stmt->execute();

        $teachers = [];
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $teachers[] = $row;
        }

        return $teachers;
    }
    public function searchStudents($searchInput)
    {
        $query = "SELECT * FROM users WHERE role = 'Student' AND (name LIKE :search OR surname LIKE :search OR username LIKE :search)";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindValue(':search', '%' . $searchInput . '%', PDO::PARAM_STR);
        $stmt->execute();

        $students = [];
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $students[] = $row;
        }

        return $students;
    }

    public function userUpdate($id, $name, $surname, $username, $password, $role)
    {
        $hashedPassword = password_hash($password, PASSWORD_ARGON2I);

        $query = "UPDATE users SET name = :name, surname = :surname, username = :username, password = :password, role = :role WHERE id = :id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":surname", $surname);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $hashedPassword);
        $stmt->bindParam(":role", $role);
        $result = $stmt->execute();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function getUser($id)
    {
        $query = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $user = new users($result["id"], $result["name"], $result["surname"], $result["username"], $result["password"], $result["role"], $result["created_at"]);
        return $user;
    }




    public function getStudentCount()
    {
        $query = "SELECT COUNT(*) FROM users WHERE role = 'student'";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchColumn();
        return $result;
    }

    public function getTeacherCount()
    {
        $query = "SELECT COUNT(*) FROM users WHERE role = 'teacher'";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchColumn();
        return $result;
    }
}


class classes
{
    public $id;
    public $class_name;
    public $class_teacher_id;


    public function __construct($id, $class_name, $class_teacher_id)
    {
        $this->id = $id;
        $this->class_name = $class_name;
        $this->class_teacher_id = $class_teacher_id;
    }
}

class classController
{
    public database $db;
    public function __construct()
    {
        $this->db = new database();
    }

    public function add($class_name, $class_teacher_id)
    {
        $query = "SELECT * FROM classes WHERE class_teacher_id = :class_teacher_id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(":class_teacher_id", $class_teacher_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return false;
        } else {
            $query = "INSERT INTO classes (class_name,class_teacher_id) VALUES (:class_name,:class_teacher_id)";
            $stmt = $this->db->getConnection()->prepare($query);
            $stmt->bindParam(":class_name", $class_name);
            $stmt->bindParam(":class_teacher_id", $class_teacher_id);
            $result = $stmt->execute();
            if ($result) {
                return true;
            } else {
                return false;
            }
        }
    }


    public function getStudentsByClass($class_id)
    {
        $query = "SELECT users.id, users.name, users.surname FROM users INNER JOIN classes_students ON users.id = classes_students.student_id WHERE classes_students.class_id = :class_id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(":class_id", $class_id);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $students = [];
        foreach ($result as $item) {
            $student = new users($item["id"], $item["name"], $item["surname"], "", "", "", "");
            array_push($students, $student);
        }
        return $students;
    }



    public function getClassByTeacher($teacher_id)
    {
        $query = "SELECT * FROM classes WHERE class_teacher_id = :teacher_id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(":teacher_id", $teacher_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $class = new classes($result["id"], $result["class_name"], $result["class_teacher_id"]);
        return $class;
    }



    public function getClasses()
    {
        $query = "SELECT classes.id, classes.class_name, users.name, users.surname FROM classes INNER JOIN users ON classes.class_teacher_id = users.id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $classes = [];
        foreach ($result as $item) {
            $class = new classes($item["id"], $item["class_name"], $item["name"] . " " . $item["surname"]);
            array_push($classes, $class);
        }
        return $classes;
    }




    public function getStudentCountInClass($class_id)
    {
        $query = "SELECT COUNT(*) FROM classes_students WHERE class_id = :class_id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(":class_id", $class_id);
        $stmt->execute();
        $result = $stmt->fetchColumn();
        return $result;
    }












    public function getClass($id)
    {
        $query = "SELECT * FROM classes WHERE id = :id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $class = new classes($result["id"], $result["class_name"], $result["class_teacher_id"]);
        return $class;
    }

    public function classUpdate($id, $class_name, $class_teacher_id)
    {
        $query = "UPDATE classes SET class_name = :class_name, class_teacher_id = :class_teacher_id WHERE id = :id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":class_name", $class_name);
        $stmt->bindParam(":class_teacher_id", $class_teacher_id);
        $result = $stmt->execute();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function searchClasses($searchInput)
    {
        $query = "SELECT classes.id, classes.class_name, users.name, users.surname 
                  FROM classes 
                  INNER JOIN users ON classes.class_teacher_id = users.id
                  WHERE classes.class_name LIKE :search";

        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindValue(':search', '%' . $searchInput . '%', PDO::PARAM_STR);
        $stmt->execute();

        $classes = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $class = new classes($row["id"], $row["class_name"], $row["name"] . " " . $row["surname"]);
            $classes[] = $class;
        }

        return $classes;
    }

    public function studentAdd($student_id, $class_id)
    {
        $query = "SELECT * FROM classes_students WHERE student_id = :student_id AND class_id = :class_id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(":student_id", $student_id);
        $stmt->bindParam(":class_id", $class_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return false;
        } else {
            $query = "INSERT INTO classes_students (student_id,class_id) VALUES (:student_id,:class_id)";
            $stmt = $this->db->getConnection()->prepare($query);
            $stmt->bindParam(":student_id", $student_id);
            $stmt->bindParam(":class_id", $class_id);
            $result = $stmt->execute();
            if ($result) {
                return true;
            } else {
                return false;
            }
        }
    }


    public function getClassCount()
    {
        $query = "SELECT COUNT(*) FROM classes";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchColumn();
        return $result;
    }
}

class lesson
{
    public $id;
    public $lesson_name;
    public $teacher_user_id;

    public function __construct($id, $lesson_name, $teacher_user_id)
    {
        $this->id = $id;
        $this->lesson_name = $lesson_name;
        $this->teacher_user_id = $teacher_user_id;
    }
}

class lessonController
{
    public database $db;
    public function __construct()
    {
        $this->db = new database();
    }

    public function lessonAdd($lesson_name, $teacher_user_id)
    {
        $query = "SELECT * FROM lessons WHERE teacher_user_id = :teacher_user_id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(":teacher_user_id", $teacher_user_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return false;
        } else {
            $query = "INSERT INTO lessons (lesson_name,teacher_user_id) VALUES (:lesson_name,:teacher_user_id)";
            $stmt = $this->db->getConnection()->prepare($query);
            $stmt->bindParam(":lesson_name", $lesson_name);
            $stmt->bindParam(":teacher_user_id", $teacher_user_id);
            $result = $stmt->execute();
            if ($result) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function getLessonByTeacher($teacher_id)
    {
        $query = "SELECT * FROM lessons WHERE teacher_user_id = :teacher_id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(":teacher_id", $teacher_id);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $lessons = [];
        foreach ($result as $item) {
            $lesson = new lesson($item["id"], $item["lesson_name"], $item["teacher_user_id"]);
            array_push($lessons, $lesson);
        }
        return $lessons;
    }

    public function getLessons()
    {
        $query = "SELECT lessons.id, lessons.lesson_name, users.name, users.surname FROM lessons INNER JOIN users ON lessons.teacher_user_id = users.id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $lessons = [];
        foreach ($result as $item) {
            $lesson = new lesson($item["id"], $item["lesson_name"], $item["name"] . " " . $item["surname"]);
            array_push($lessons, $lesson);
        }
        return $lessons;
    }

    public function getLesson($id)
    {
        $query = "SELECT * FROM lessons WHERE id = :id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $lesson = new lesson($result["id"], $result["lesson_name"], $result["teacher_user_id"]);
        return $lesson;
    }

    public function lessonUpdate($id, $lesson_name, $teacher_user_id)
    {
        $query = "UPDATE lessons SET lesson_name = :lesson_name, teacher_user_id = :teacher_user_id WHERE id = :id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":lesson_name", $lesson_name);
        $stmt->bindParam(":teacher_user_id", $teacher_user_id);
        $result = $stmt->execute();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function searchLessons($searchInput)
    {
        $query = "SELECT lessons.id, lessons.lesson_name, users.name, users.surname
                  FROM lessons
                  INNER JOIN users ON lessons.teacher_user_id = users.id
                  WHERE lessons.lesson_name LIKE :search";

        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindValue(':search', '%' . $searchInput . '%', PDO::PARAM_STR);
        $stmt->execute();

        $lessons = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $lesson = new Lesson($row["id"], $row["lesson_name"], $row["name"] . " " . $row["surname"]);
            $lessons[] = $lesson;
        }

        return $lessons;
    }


    public function getLessonsByTeacher($teacher_id)
    {
        $query = "SELECT * FROM lessons WHERE teacher_user_id = :teacher_id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(":teacher_id", $teacher_id);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $lessons = [];
        foreach ($result as $item) {
            $lesson = new lesson($item["id"], $item["lesson_name"], $item["teacher_user_id"]);
            array_push($lessons, $lesson);
        }
        return $lessons;
    }

    public function getLessonsByStudent($student_id)
    {
        $query = "SELECT lessons.id, lessons.lesson_name, users.name, users.surname FROM lessons INNER JOIN classes_students ON lessons.id = classes_students.class_id INNER JOIN users ON lessons.teacher_user_id = users.id WHERE classes_students.student_id = :student_id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(":student_id", $student_id);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $lessons = [];
        foreach ($result as $item) {
            $lesson = new lesson($item["id"], $item["lesson_name"], $item["name"] . " " . $item["surname"]);
            array_push($lessons, $lesson);
        }
        return $lessons;
    }

    public function getLessonCount()
    {
        $query = "SELECT COUNT(*) FROM lessons";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchColumn();
        return $result;
    }
}

class examScore
{
    public $id;
    public $student_id;
    public $lesson_id;
    public $class_id;
    public $exam_score;
    public $teacher_name;
    public $exam_date;


    public function __construct($id, $student_id, $lesson_id, $class_id, $exam_score, $exam_date, $teacher_name = null)
    {
        $this->id = $id;
        $this->student_id = $student_id;
        $this->lesson_id = $lesson_id;
        $this->class_id = $class_id;
        $this->exam_score = $exam_score;
        $this->exam_date = $exam_date;
        $this->teacher_name = $teacher_name;
    }
}

class examScoreController
{
    public database $db;
    public function __construct()
    {
        $this->db = new database();
    }

    public function examAdd($student_id, $lesson_id, $class_id, $exam_score)
    {
        $query = "SELECT * FROM exams WHERE student_id = :student_id AND lesson_id = :lesson_id AND class_id = :class_id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(":student_id", $student_id);
        $stmt->bindParam(":lesson_id", $lesson_id);
        $stmt->bindParam(":class_id", $class_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return false;
        } else {
            $query = "INSERT INTO exams (student_id, lesson_id, class_id, exam_score) VALUES (:student_id, :lesson_id, :class_id, :exam_score)";
            $stmt = $this->db->getConnection()->prepare($query);
            $stmt->bindParam(":student_id", $student_id);
            $stmt->bindParam(":lesson_id", $lesson_id);
            $stmt->bindParam(":class_id", $class_id);
            $stmt->bindParam(":exam_score", $exam_score);
            $result = $stmt->execute();
            if ($result) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function getExamScores()
    {
        $query = "SELECT exams.id, users.name, users.surname, lessons.lesson_name, classes.class_name, exams.exam_score, exams.exam_date FROM exams INNER JOIN users ON exams.student_id = users.id INNER JOIN lessons ON exams.lesson_id = lessons.id INNER JOIN classes ON exams.class_id = classes.id WHERE lessons.teacher_user_id = :teacher_id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->execute(array(':teacher_id' => $_SESSION["user"]["id"]));
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $examScores = [];
        foreach ($result as $item) {
            $examScore = new examScore($item["id"], $item["name"] . " " . $item["surname"], $item["lesson_name"], $item["class_name"], $item["exam_score"], $item["exam_date"]);
            array_push($examScores, $examScore);
        }
        return $examScores;
    }
    public function getExamScoreByStudent($search)
    {
        $query = "SELECT exams.id, users.name, users.surname, lessons.lesson_name, classes.class_name, exams.exam_score, exams.exam_date FROM exams INNER JOIN users ON exams.student_id = users.id INNER JOIN lessons ON exams.lesson_id = lessons.id INNER JOIN classes ON exams.class_id = classes.id WHERE users.name LIKE :search OR users.surname LIKE :search";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $examScores = [];
        foreach ($result as $item) {
            $examScore = new examScore($item["id"], $item["name"] . " " . $item["surname"], $item["lesson_name"], $item["class_name"], $item["exam_score"], $item["exam_date"]);
            array_push($examScores, $examScore);
        }
        return $examScores;
    }

    public function examUpdate($id, $exam_score)
    {
        $query = "UPDATE exams SET exam_score = :exam_score WHERE id = :id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":exam_score", $exam_score);
        $result = $stmt->execute();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }




    public function getExamScoreById($id)
    {
        $query = "SELECT * FROM exams WHERE id = :id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $examScore = new examScore($result["id"], $result["student_id"], $result["lesson_id"], $result["class_id"], $result["exam_score"], $result["exam_date"]);
        return $examScore;
    }
    public function getStudentNameById($studentId)
    {
        $query = "SELECT * FROM users WHERE id = :studentId";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(":studentId", $studentId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result["name"] . " " . $result["surname"];
    }



    public function getStudentName($id)
    {
        $query = "SELECT users.name, users.surname FROM users INNER JOIN exams ON users.id = exams.student_id WHERE exams.id = :id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $studentName = $result["name"] . " " . $result["surname"];
        return $studentName;
    }

    public function avarage()
    {
        $query = "SELECT AVG(exams.exam_score) FROM exams INNER JOIN users ON exams.student_id = users.id INNER JOIN lessons ON exams.lesson_id = lessons.id INNER JOIN classes ON exams.class_id = classes.id WHERE lessons.teacher_user_id = :teacher_id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->execute(array(':teacher_id' => $_SESSION["user"]["id"]));
        $result = $stmt->fetchColumn();
        return $result;
    }

    public function studentLessonCount()
    {
        $query = "SELECT COUNT(*) FROM exams INNER JOIN users ON exams.student_id = users.id INNER JOIN lessons ON exams.lesson_id = lessons.id INNER JOIN classes ON exams.class_id = classes.id WHERE lessons.teacher_user_id = :teacher_id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->execute(array(':teacher_id' => $_SESSION["user"]["id"]));
        $result = $stmt->fetchColumn();
        return $result;
    }

    public function studentExamCount($student_id)
    {
        $query = "SELECT COUNT(*) FROM exams WHERE student_id = :student_id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(":student_id", $student_id);
        $stmt->execute();
        $result = $stmt->fetchColumn();
        return $result;
    }

    public function studentAvarage($student_id)
    {
        $query = "SELECT AVG(exams.exam_score) FROM exams WHERE student_id = :student_id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(":student_id", $student_id);
        $stmt->execute();
        $result = $stmt->fetchColumn();
        return $result;
    }

    public function studentClass($student_id)
    {
        $query = "SELECT classes.class_name FROM classes INNER JOIN classes_students ON classes.id = classes_students.class_id WHERE classes_students.student_id = :student_id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(":student_id", $student_id);
        $stmt->execute();
        $result = $stmt->fetchColumn();
        return $result;
    }

    public function studentExamScore($student_id)
    {
        $query = "SELECT users.name, users.surname, classes.class_name, lessons.lesson_name, exams.exam_score FROM exams INNER JOIN users ON exams.student_id = users.id INNER JOIN lessons ON exams.lesson_id = lessons.id INNER JOIN classes ON exams.class_id = classes.id WHERE exams.student_id = :student_id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(":student_id", $student_id);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $examScores = [];
        foreach ($result as $item) {
            $examScore = new examScore($item["id"] = null, $item["name"] . " " . $item["surname"], $item["lesson_name"], $item["class_name"], $item["exam_score"], $item["exam_date"] = null);
            array_push($examScores, $examScore);
        }
        return $examScores;
    }

    public function getExamScoreByStudentId($student_id)
    {
        $query = "SELECT exams.id, users.name, users.surname, lessons.lesson_name, classes.class_name, exams.exam_score, exams.exam_date FROM exams INNER JOIN users ON exams.student_id = users.id INNER JOIN lessons ON exams.lesson_id = lessons.id INNER JOIN classes ON exams.class_id = classes.id WHERE exams.student_id = :student_id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(":student_id", $student_id);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $examScores = [];
        foreach ($result as $item) {
            $examScore = new examScore($item["id"], $item["name"] . " " . $item["surname"], $item["lesson_name"], $item["class_name"], $item["exam_score"], $item["exam_date"]);
            array_push($examScores, $examScore);
        }
        return $examScores;
    }
}


class classesStudents
{
    public $id;

    public $student_id;

    public $class_id;

    public function __construct($id, $student_id, $class_id)
    {
        $this->id = $id;
        $this->student_id = $student_id;
        $this->class_id = $class_id;
    }
}
class studentController
{
    public database $db;
    public function __construct()
    {
        $this->db = new database();
    }



    public function getStudentsByClass($class_id)
    {
        $query = "SELECT users.id, users.name, users.surname FROM users INNER JOIN classes_students ON users.id = classes_students.student_id WHERE classes_students.class_id = :class_id";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindParam(":class_id", $class_id);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $students = [];
        foreach ($result as $item) {
            $student = new users($item["id"], $item["name"], $item["surname"], "", "", "", "");
            array_push($students, $student);
        }
        return $students;
    }
}