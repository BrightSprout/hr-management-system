<?php
  namespace App\Model;

  use App\Helper\QueryBuilder;
  
  class EmployeeDocumentModel {
    public string $id;
    public string $employee_id;
    public ?string $type;
    public string $url;

    public function __construct(array $data) {
      $this->id = $data["id"];
      $this->employee_id = $data["employee_id"];
      $this->type = $data["type"] ?? NULL;
      $this->url = $data["url"];
    }

    public static function listDocuments($db): array {
      $sql = "SELECT * FROM employee_documents"; 
      $result = $db->query($sql);
      $documents = [];

      while ($row = $result->fetch_assoc()) {
        $documents[$row["id"]] = new EmployeeDocumentModel([
          "id" => $row["id"],
          "employee_id" => $row["employee_id"],
          "type" => $row["type"],
          "url" => $row["url"], 
        ]);
      }

      return $documents;
    } 

    public static function listByEmployeeId($db, array $data): array {
      $sql = "SELECT * FROM employee_documents WHERE employee_id = ?"; 
      $stmt = $db->prepare($sql);
      $stmt->bind_param("s", $data["employee_id"]);
      $stmt->execute();
      $result = $stmt->get_result();
      $documents = [];

      while ($row = $result->fetch_assoc()) {
        $documents[$row["id"]] = new EmployeeDocumentModel([
          "id" => $row["id"],
          "employee_id" => $row["employee_id"],
          "type" => $row["type"],
          "url" => $row["url"], 
        ]);
      }

      return $documents;
    }

    public static function listByUserId($db, array $data): array {
      $sql = "SELECT * FROM employee_documents WHERE employee_id = (SELECT e.id FROM employees e WHERE e.user_id = ?)"; 
      $stmt = $db->prepare($sql);
      $stmt->bind_param("s", $data["user_id"]);
      $stmt->execute();
      $result = $stmt->get_result();
      $documents = [];

      while ($row = $result->fetch_assoc()) {
        $documents[$row["id"]] = new EmployeeDocumentModel([
          "id" => $row["id"],
          "employee_id" => $row["employee_id"],
          "type" => $row["type"],
          "url" => $row["url"], 
        ]);
      }

      return $documents;
    }

    public static function create($db, array $data): EmployeeDocumentModel {
      $sql = "INSERT INTO employee_documents (id, employee_id, type, url) VALUES (?,?,?,?)";
      $db->query("SET time_zone = '+00:00';");
      $stmt = $db->prepare($sql);
      $stmt->bind_param("ssss",
        $data["id"],
        $data["employee_id"],
        $data["type"],
        $data["url"],
      );

      if (!$stmt->execute()) {
        throw new \Exception("Insertion Failed!");
      }  
      
      return new EmployeeDocumentModel([
        "id" => $data["id"],
        "employee_id" => $data["employee_id"],
        "type" => $data["type"],
        "url" => $data["url"], 
      ]);
    }

    public static function update($db, array $data): bool {
      $query = QueryBuilder::buildDynamicUpdate("employee_documents", $data["id"], $data["document"]);
      $db->query("SET time_zone = '+00:00';");
      $stmt = $db->prepare($query["sql"]);
      $params = $query["params"];
      $tmp = [];
      foreach ($params as $key => $value) {
        $tmp[$key] = &$params[$key];
      }
      call_user_func_array([$stmt, "bind_param"], $tmp);
      return $stmt->execute();
    }

    public static function delete($db, array $data): bool {
      $sql = "DELETE FROM employee_documents WHERE id = ?";
      $stmt = $db->prepare($sql);
      $stmt->bind_param("s", $data["id"]);
      return $stmt->execute();
    }
  }
?>
