# API Documentation - Système d'Archivage

## Overview

This API provides external platforms access to document data and files from the Université du Burundi archiving system.

**Base URL:** `http://your-domain.com/api/v1`

## Authentication

No authentication required — the API endpoints used to receive documents and to retrieve errors are intentionally left open to allow the external system to push files without tokens. If you later want to add a token or signature, we can enable it, but for now the examples below assume no token.

## Endpoints

### 1. Get All Documents

Retrieve a paginated list of all documents with full metadata.

**Endpoint:** `GET /api/v1/documents`

**Parameters:**

-   `per_page` (optional): Number of items per page (default: 50, max: 100)

**Example Request:**

```bash
curl "http://your-domain.com/api/v1/documents?per_page=20"
```

**Example Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "titre": "Mémoire de Licence",
            "statut": "archive",
            "statut_label": "Archivé",
            "fichier": {
                "nom_original": "memoire_final.pdf",
                "nom_stocke": "1234567890_memoire.pdf",
                "extension": "pdf",
                "taille": 2048576,
                "taille_formatee": "2.00 MB",
                "url_download": "http://your-domain.com/api/v1/documents/1/download"
            },
            "etudiant": {
                "id": 1,
                "nom": "NDAYIZEYE",
                "prenom": "Jean",
                "matricule": "UB2024001",
                "email": "jean@student.uniburundi.bi",
                "faculte": "Sciences",
                "departement": "Informatique"
            },
            "type_document": {
                "id": 1,
                "nom": "Mémoire",
                "code": "MEM",
                "description": "Mémoire de fin d'études"
            },
            "traitement": {
                "remarques": "Document validé",
                "date_traitement": "2024-10-20 14:30:00",
                "traite_par": {
                    "id": 1,
                    "nom": "NKURUNZIZA",
                    "prenom": "Marie",
                    "email": "admin@uniburundi.bi"
                }
            },
            "archivage": {
                "reference": "UB-2024-ABC123",
                "emplacement_physique": "Salle A, Étagère 5, Boîte 12",
                "notes": "Archivé avec annexes",
                "date_archivage": "2024-10-21 10:00:00",
                "archive_par": {
                    "id": 1,
                    "nom": "NKURUNZIZA",
                    "prenom": "Marie"
                }
            },
            "created_at": "2024-10-15 09:00:00",
            "updated_at": "2024-10-21 10:00:00"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 20,
        "total": 95
    },
    "links": {
        "first": "http://your-domain.com/api/v1/documents?page=1",
        "last": "http://your-domain.com/api/v1/documents?page=5",
        "prev": null,
        "next": "http://your-domain.com/api/v1/documents?page=2"
    }
}
```

---

### 2. Get Single Document

Retrieve detailed information about a specific document.

**Endpoint:** `GET /api/v1/documents/{id}`

**Example Request:**

```bash
curl "http://your-domain.com/api/v1/documents/1"
```

**Example Response:**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "titre": "Mémoire de Licence",
    "statut": "archive",
    "fichier": { ... },
    "etudiant": { ... },
    "type_document": { ... },
    "traitement": { ... },
    "archivage": { ... },
    "created_at": "2024-10-15 09:00:00",
    "updated_at": "2024-10-21 10:00:00"
  }
}
```

**Error Response (404):**

```json
{
    "success": false,
    "message": "Document not found"
}
```

---

### 3. Download Document File

Download the actual document file.

**Endpoint:** `GET /api/v1/documents/{id}/download`

**Example Request:**

```bash
curl -O -J "http://your-domain.com/api/v1/documents/1/download"
```

**Response:** Binary file download with appropriate headers

**Error Response (404):**

```json
{
    "success": false,
    "message": "File not found on server"
}
```

---

### 4. Get Documents by Status

Retrieve documents filtered by their status.

**Endpoint:** `GET /api/v1/documents/status/{status}`

**Valid Status Values:**

-   `en_attente` - Pending validation
-   `valide` - Validated
-   `rejete` - Rejected
-   `archive` - Archived

**Parameters:**

-   `per_page` (optional): Number of items per page (default: 50, max: 100)

**Example Request:**

```bash
curl "http://your-domain.com/api/v1/documents/status/archive?per_page=30"
```

**Example Response:**

```json
{
  "success": true,
  "data": [ ... ],
  "meta": {
    "status": "archive",
    "current_page": 1,
    "last_page": 3,
    "per_page": 30,
    "total": 75
  },
  "links": { ... }
}
```

**Error Response (400):**

```json
{
    "success": false,
    "message": "Invalid status. Valid statuses: en_attente, valide, rejete, archive"
}
```

---

### 5. Get Archived Documents

Retrieve all archived documents with archive-specific information.

**Endpoint:** `GET /api/v1/archives`

**Parameters:**

-   `per_page` (optional): Number of items per page (default: 50, max: 100)

**Example Request:**

```bash
curl "http://your-domain.com/api/v1/archives"
```

**Example Response:**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "reference_archive": "UB-2024-ABC123",
      "emplacement_physique": "Salle A, Étagère 5, Boîte 12",
      "notes_archivage": "Archivé avec annexes",
      "date_archivage": "2024-10-21 10:00:00",
      "document": {
        "id": 1,
        "titre": "Mémoire de Licence",
        "statut": "archive",
        "fichier": {
          "nom_original": "memoire_final.pdf",
          "extension": "pdf",
          "taille": 2048576,
          "taille_formatee": "2.00 MB",
          "url_download": "http://your-domain.com/api/v1/documents/1/download"
        },
        "etudiant": {
          "id": 1,
          "nom": "NDAYIZEYE",
          "prenom": "Jean",
          "matricule": "UB2024001",
          "email": "jean@student.uniburundi.bi",
          "faculte": "Sciences"
        },
        "type_document": {
          "id": 1,
          "nom": "Mémoire",
          "code": "MEM"
        },
        "created_at": "2024-10-15 09:00:00"
      },
      "archive_par": {
        "id": 1,
        "nom": "NKURUNZIZA",
        "prenom": "Marie",
        "email": "admin@uniburundi.bi"
      },
      "created_at": "2024-10-21 10:00:00",
      "updated_at": "2024-10-21 10:00:00"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 50,
    "total": 125
  },
  "links": { ... }
}
```

---

## Error Responses

### Authentication Errors

**401 Unauthorized - Missing Token:**

```json
{
    "success": false,
    "message": "API token is required. Provide it via X-API-Token header or api_token parameter"
}
```

**403 Forbidden - Invalid Token:**

```json
{
    "success": false,
    "message": "Invalid API token"
}
```

**500 Server Error - Not Configured:**

```json
{
    "success": false,
    "message": "API authentication is not configured on the server"
}
```

---

## Usage Examples

### Python Example

```python
import requests

API_BASE_URL = "http://your-domain.com/api/v1"

# Get all documents
response = requests.get(f"{API_BASE_URL}/documents")
documents = response.json()

# Download a specific document
doc_id = 1
response = requests.get(
  f"{API_BASE_URL}/documents/{doc_id}/download",
  stream=True
)

if response.status_code == 200:
    with open("downloaded_document.pdf", "wb") as f:
        for chunk in response.iter_content(chunk_size=8192):
            f.write(chunk)
```

### JavaScript/Node.js Example

```javascript
const axios = require("axios");
const fs = require("fs");

const API_BASE_URL = "http://your-domain.com/api/v1";

// Get all documents
async function getAllDocuments() {
    try {
        const response = await axios.get(`${API_BASE_URL}/documents`);
        console.log(response.data);
    } catch (error) {
        console.error("Error:", error.message);
    }
}

// Download a document
async function downloadDocument(docId) {
    try {
        const response = await axios.get(
            `${API_BASE_URL}/documents/${docId}/download`,
            { headers, responseType: "stream" }
        );

        const writer = fs.createWriteStream("downloaded_document.pdf");
        response.data.pipe(writer);

        return new Promise((resolve, reject) => {
            writer.on("finish", resolve);
            writer.on("error", reject);
        });
    } catch (error) {
        console.error("Error:", error.response.data);
    }
}

getAllDocuments();
downloadDocument(1);
```

### PHP Example

```php
<?php

$apiBaseUrl = 'http://your-domain.com/api/v1';

// Get all documents
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$apiBaseUrl/documents");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$documents = json_decode($response, true);
curl_close($ch);

print_r($documents);

// Download a document
$docId = 1;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$apiBaseUrl/documents/$docId/download");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-API-Token: $apiToken"
]);

$fileContent = curl_exec($ch);
file_put_contents('downloaded_document.pdf', $fileContent);
curl_close($ch);
```

---

### Push (incoming) — How the external system should send files to us

If the external system prefers to push documents to us (recommended so imports are instant), it must POST the file as multipart/form-data to our incoming endpoint.

Endpoint:

POST /api/incoming-documents

Headers:

-   X-API-Token: YOUR_SHARED_TOKEN (if the server is configured with `EXTERNAL_API_TOKEN` or `API_TOKEN`)

Form fields (multipart):

-   file: binary file (PDF, DOCX, etc.)
-   uploader_id: string (an identifier the external system uses for the uploader)
-   callback_url: optional URL where we will POST detected errors (we'll store this per document)

Example curl:

```bash
curl -X POST "https://your-domain.com/api/incoming-documents" \
  -H "X-API-Token: YOUR_SHARED_TOKEN" \
  -F "uploader_id=12345" \
  -F "callback_url=https://external.example.com/callback" \
  -F "file=@/path/to/file.pdf"
```

Response (201):

```json
{ "ok": true, "id": 42, "errors": 1 }
```

Notes:

-   If an API token is set in our `.env` (`EXTERNAL_API_TOKEN` or `API_TOKEN`), the header is required. If no token is configured, the endpoint accepts public POSTs (not recommended).
-   On receipt we immediately extract text, compute MinHash, run the configured checks (basic rules + similarity) and persist any detected errors to `incoming_document_errors`.
-   If `callback_url` is provided in the request (or configured globally via `EXTERNAL_CALLBACK_URL`), an admin may trigger a POST back to the external system with the detected errors (or you can automate that with a queue/cron). The callback payload looks like:

```json
{
    "uploader_id": "12345",
    "document_id": 42,
    "errors": [{ "type": "too_short", "message": "Document trop court" }]
}
```

---

## Rate Limiting

Currently, there are no rate limits implemented. However, please be considerate with your API usage to avoid overloading the server.

## Support

For API support or to report issues, contact the system administrator at: admin@uniburundi.bi

---

## Changelog

### Version 1.0.0 (October 2024)

-   Initial API release
-   Document listing and retrieval
-   File download capability
-   Status-based filtering
-   Archive information access

---

## External error reporting (new)

L'endpoint suivant permet à une plateforme externe de signaler des erreurs détectées sur un document afin que l'étudiant soit notifié et puisse soumettre une correction.

_Méthode:_ PATCH
_Endpoint:_ `/api/v1/documents/{id}/erreurs`
_Authentification:_ Aucune requise

### Paramètres

URL Parameter:

-   `id` (requis) : ID du document dans le système cible

Body Parameters (JSON):

-   `erreur_trouve` (requis, string) : Description textuelle des erreurs détectées

### Exemple cURL

```bash
curl -X PATCH http://10.235.242.237/api/v1/documents/1/erreurs \
  -H "Content-Type: application/json" \
  -d '{
    "erreur_trouve": "Les erreurs suivantes ont été détectées:\n1. Format de date incorrect à la page 3\n2. Références bibliographiques manquantes\n3. Numérotation des sections incohérente"
  }'
```

### Comportement serveur

-   Le serveur recherche le document par `id`. Si introuvable, renvoie 404.
-   Si trouvé, un enregistrement `DocumentError` est créé avec `error_type = 'external'` et `message = erreur_trouve`.
-   Le propriétaire du document (utilisateur) est notifié par email (ou via le driver de mail configuré).
-   Réponse JSON 200 en cas de succès : `{ "message": "Erreur enregistrée", "id": <document_error_id> }`
