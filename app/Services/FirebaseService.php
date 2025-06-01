<?php

// app/Services/FirebaseService.php

// app/Services/FirebaseService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FirebaseService
{
    protected $baseUrl;
    protected $collection;
    protected $aspirasi;
    protected $jurnal;



    public function __construct()
    {
        $projectId = env('FIREBASE_PROJECT_ID'); // isi di .env
        $this->baseUrl = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents";
        $this->collection = 'Users';
        $this->aspirasi = 'aspirasi';
        $this->jurnal = 'student_cases';
    }

    public function findByEmail($email)
    {
        $all = $this->getAll();

        if (!isset($all['documents'])) return null;

        foreach ($all['documents'] as $doc) {
            // Ensure 'fields' key exists in the document
            if (!isset($doc['fields'])) {
                continue; // Skip this document if fields are missing
            }
            $fields = $doc['fields'];

            // **SAFER CHECK HERE**
            if (isset($fields['email']['stringValue']) && $fields['email']['stringValue'] === $email) {
                $id = basename($doc['name']);
                return ['id' => $id, 'fields' => $fields];
            }
        }
        return null;
    }

    public function findByUsername($username)
    {
        $all = $this->getAll();

        if (!isset($all['documents'])) return null;

        foreach ($all['documents'] as $doc) {
            // Ensure 'fields' key exists in the document
            if (!isset($doc['fields'])) {
                continue; // Skip this document if fields are missing
            }
            $fields = $doc['fields'];

            // This check is already good
            if (isset($fields['username']['stringValue']) && $fields['username']['stringValue'] === $username) {
                $id = basename($doc['name']);
                return ['id' => $id, 'fields' => $fields];
            }
        }
        return null;
    }

    public function getAll()
    {
        $url = "{$this->baseUrl}/{$this->collection}";
        $response = Http::get($url);
        return $response->json();
    }
    public function getAllJurnal()
    {
        $url = "{$this->baseUrl}/{$this->jurnal}";
        $response = Http::get($url);
        return $response->json();
    }

    public function create(array $data)
    {
        $url = "{$this->baseUrl}/{$this->collection}";
        $body = [
            'fields' => $this->formatData($data)
        ];
        $response = Http::post($url, $body);
        return $response->json();
    }

    public function createjurnal(array $data)
    {
        $url = "{$this->baseUrl}/{$this->jurnal}";
        $body = [
            'fields' => $this->formatData($data)
        ];
        $response = Http::post($url, $body);
        return $response->json();
    }

    public function createDocumentWithSpecificId(string $aspirasi, array $data, string $documentId)
{
    // $this->baseUrl should be like "https://firestore.googleapis.com/v1/projects/YOUR_PROJECT_ID/databases/(default)/documents"
    $url = "{$this->baseUrl}/{$aspirasi}?documentId={$documentId}";
    $body = [
        'fields' => $this->formatData($data) // Assuming formatData correctly prepares Firestore field types
    ];
    // Important: When using POST with documentId, Firestore creates the document.
    // If the document already exists, this will result in an error (ALREADY_EXISTS).
    $response = Http::post($url, $body);
    return $response->json();
}

    public function get($id)
    {
        $url = "{$this->baseUrl}/{$this->collection}/{$id}";
        $response = Http::get($url);
        return $response->json();
    }
    
    public function getJurnal($id)
{
    $url = "{$this->baseUrl}/{$this->jurnal}/{$id}";
    $response = Http::get($url);

    if ($response->successful()) {
        return $response->json();
    }

    // Jika error, kembalikan null atau throw exception
    return null;
}

    public function update($id, array $data)
    {
        $url = "{$this->baseUrl}/{$this->collection}/{$id}?updateMask.fieldPaths=" . implode("&updateMask.fieldPaths=", array_keys($data));
        $body = ['fields' => $this->formatData($data)];
        $response = Http::patch($url, $body);
        return $response->json();
    }

    public function updateJurnal($id, array $data)
    {
        $url = "{$this->baseUrl}/{$this->jurnal}/{$id}?updateMask.fieldPaths=" . implode("&updateMask.fieldPaths=", array_keys($data));
        $body = ['fields' => $this->formatData($data)];
        $response = Http::patch($url, $body);
        return $response->json();
    }

    public function delete($id)
    {
        $url = "{$this->baseUrl}/{$this->collection}/{$id}";
        return Http::delete($url);
    }
    public function deleteJurnal($id)
    {
        $url = "{$this->baseUrl}/{$this->jurnal}/{$id}";
        return Http::delete($url);
    }

    // ------------- Functions for FORMATTING data TO Firestore -------------

    protected function formatData(array $data): array
    {
        $formattedFields = [];
        foreach ($data as $key => $value) {
            $formattedFields[$key] = $this->formatSingleValueToFirestoreType($value);
        }
        return $formattedFields;
    }

    protected function formatSingleValueToFirestoreType($value): array
    {
        if (is_null($value)) {
            return ['nullValue' => null];
        } elseif (is_int($value)) {
            return ['integerValue' => (string)$value];
        } elseif (is_float($value)) {
            return ['doubleValue' => $value];
        } elseif (is_bool($value)) {
            return ['booleanValue' => $value];
        } elseif ($this->isDate($value)) {
            return ['timestampValue' => date(DATE_RFC3339, strtotime($value))];
        } elseif (is_string($value)) {
            return ['stringValue' => $value];
        } elseif (is_array($value)) {
            $isList = empty($value) || (array_keys($value) === range(0, count($value) - 1));
            if ($isList) {
                return [
                    'arrayValue' => [
                        'values' => array_map([$this, 'formatSingleValueToFirestoreType'], $value)
                    ]
                ];
            } else {
                return [
                    'mapValue' => [
                        'fields' => $this->formatData($value)
                    ]
                ];
            }
        } else {
            return ['stringValue' => (string) $value];
        }
    }

    protected function isDate($value): bool
    {
        if (!is_string($value)) {
            return false;
        }
        // Allow YYYY-MM-DD or full ISO 8601 timestamps for 'created_at', 'updated_at'
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) && strtotime($value) !== false) {
            return true;
        }
        // Check for ISO 8601 format like 2025-05-31T10:30:00.000000Z or 2025-05-31T17:30:00+07:00
        if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d+)?(?:Z|[+-]\d{2}:\d{2})$/', $value) && strtotime($value) !== false) {
            return true;
        }
        return false;
    }


    public function createAspiration(array $data)
{
        $url = "{$this->baseUrl}/{$this->aspirasi}";
        $body = [
            'fields' => $this->formatData($data)
        ];
        $response = Http::post($url, $body);
        return $response->json();
}


public function getAllAspirations()
{
    $url = "{$this->baseUrl}/{$this->aspirasi}"; // Misal $this->aspirasi adalah nama koleksi
    $response = Http::get($url);
    return $response->json();
}

/**
 * Get documents from the 'aspirations' collection for a specific user.
 * (Untuk Siswa melihat aspirasi sendiri)
 *
 * @param string $userId
 * @return array
 */
public function getAspirationsByUserId($id)
{
  $url = "{$this->baseUrl}/{$this->aspirasi}/{$id}";
        $response = Http::get($url);
        return $response->json();
}

public function deleteAspiration($id)
    {
        $url = "{$this->baseUrl}/{$this->aspirasi}/{$id}";
        return Http::delete($url);
    }
// Pastikan Anda memiliki helper seperti prepareFirestoreData jika dibutuhkan
// private function prepareFirestoreData(array $data) { ... }
// Dan metode generik seperti createDocumentInCollection atau getAllDocumentsFromCollection jika ada
// public function createDocumentInCollection(string $collectionName, array $data, ?string $documentId = null) { ... }
// public function getAllDocumentsFromCollection(string $collectionName) { ... }



}