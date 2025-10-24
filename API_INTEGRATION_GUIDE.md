# API Integration Guide
## Système d'Archivage - Université du Burundi

### Base URL
```
http://your-domain.com/api/v1
```

### Authentication
**No authentication required** - All endpoints are publicly accessible.

---

## Available Endpoints

### 1. Get All Documents
**Endpoint:** `GET /api/v1/documents`

**Parameters:**
- `per_page` (optional): Items per page (default: 50, max: 100)
- `page` (optional): Page number

**Example Request:**
```bash
curl http://your-domain.com/api/v1/documents?per_page=20&page=1
```

**Response:**
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
        "nom_stocke": "be8c85cd-989f-4212-904f-a130ee51acde.pdf",
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
        "emplacement_physique": "Salle A, Étagère 5",
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
**Endpoint:** `GET /api/v1/documents/{id}`

**Example Request:**
```bash
curl http://your-domain.com/api/v1/documents/1
```

**Response:** Same structure as single document in list above.

---

### 3. Download Document File
**Endpoint:** `GET /api/v1/documents/{id}/download`

**Example Request:**
```bash
# Download to file
curl http://your-domain.com/api/v1/documents/1/download -o document.pdf

# Or with original filename
curl -O -J http://your-domain.com/api/v1/documents/1/download
```

**Response:** Binary file download (PDF, DOCX, etc.)

---

### 4. Filter by Status
**Endpoint:** `GET /api/v1/documents/status/{status}`

**Valid Status Values:**
- `en_attente` - Pending validation
- `valide` - Validated
- `rejete` - Rejected
- `archive` - Archived

**Parameters:**
- `per_page` (optional): Items per page
- `page` (optional): Page number

**Example Request:**
```bash
curl http://your-domain.com/api/v1/documents/status/archive?per_page=30
```

**Response:** Same structure as document list.

---

### 5. Get Archived Documents
**Endpoint:** `GET /api/v1/archives`

**Parameters:**
- `per_page` (optional): Items per page
- `page` (optional): Page number

**Example Request:**
```bash
curl http://your-domain.com/api/v1/archives
```

**Response:**
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
        "etudiant": { ... },
        "type_document": { ... }
      },
      "archive_par": {
        "id": 1,
        "nom": "NKURUNZIZA",
        "prenom": "Marie",
        "email": "admin@uniburundi.bi"
      }
    }
  ],
  "meta": { ... },
  "links": { ... }
}
```

---

## Implementation Examples

### Python
```python
import requests

BASE_URL = "http://your-domain.com/api/v1"

# Get all documents
response = requests.get(f"{BASE_URL}/documents")
data = response.json()

for doc in data['data']:
    print(f"Document: {doc['titre']}")
    print(f"Student: {doc['etudiant']['nom']} {doc['etudiant']['prenom']}")
    print(f"Status: {doc['statut_label']}")
    print(f"Download: {doc['fichier']['url_download']}")
    print("-" * 50)

# Download a specific document
doc_id = 1
response = requests.get(f"{BASE_URL}/documents/{doc_id}/download", stream=True)
if response.status_code == 200:
    with open(f"document_{doc_id}.pdf", "wb") as f:
        for chunk in response.iter_content(chunk_size=8192):
            f.write(chunk)
    print("Download complete!")

# Get only archived documents
response = requests.get(f"{BASE_URL}/archives")
archives = response.json()
print(f"Total archived: {archives['meta']['total']}")
```

### JavaScript/Node.js
```javascript
const axios = require('axios');
const fs = require('fs');

const BASE_URL = 'http://your-domain.com/api/v1';

// Get all documents
async function getAllDocuments() {
  try {
    const response = await axios.get(`${BASE_URL}/documents`);
    const { data, meta } = response.data;
    
    console.log(`Total documents: ${meta.total}`);
    
    data.forEach(doc => {
      console.log(`${doc.titre} - ${doc.statut_label}`);
      console.log(`Student: ${doc.etudiant.nom} ${doc.etudiant.prenom}`);
      console.log(`Download: ${doc.fichier.url_download}`);
      console.log('-'.repeat(50));
    });
  } catch (error) {
    console.error('Error:', error.message);
  }
}

// Download a document
async function downloadDocument(docId) {
  try {
    const response = await axios.get(
      `${BASE_URL}/documents/${docId}/download`,
      { responseType: 'stream' }
    );
    
    const writer = fs.createWriteStream(`document_${docId}.pdf`);
    response.data.pipe(writer);
    
    return new Promise((resolve, reject) => {
      writer.on('finish', () => {
        console.log('Download complete!');
        resolve();
      });
      writer.on('error', reject);
    });
  } catch (error) {
    console.error('Error:', error.message);
  }
}

// Get filtered documents
async function getValidatedDocuments() {
  try {
    const response = await axios.get(`${BASE_URL}/documents/status/valide`);
    console.log(`Validated documents: ${response.data.meta.total}`);
  } catch (error) {
    console.error('Error:', error.message);
  }
}

// Run examples
getAllDocuments();
downloadDocument(1);
getValidatedDocuments();
```

### PHP
```php
<?php

$baseUrl = 'http://your-domain.com/api/v1';

// Get all documents
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/documents");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

foreach ($data['data'] as $doc) {
    echo "Document: {$doc['titre']}\n";
    echo "Student: {$doc['etudiant']['nom']} {$doc['etudiant']['prenom']}\n";
    echo "Status: {$doc['statut_label']}\n";
    echo "Download: {$doc['fichier']['url_download']}\n";
    echo str_repeat('-', 50) . "\n";
}

// Download a document
$docId = 1;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/documents/$docId/download");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$fileContent = curl_exec($ch);
curl_close($ch);

file_put_contents("document_$docId.pdf", $fileContent);
echo "Download complete!\n";

// Get archives
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/archives");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$archives = json_decode($response, true);
echo "Total archived: {$archives['meta']['total']}\n";
```

---

## Data Structure Reference

### Document Object
```json
{
  "id": integer,
  "titre": string,
  "statut": "en_attente|valide|rejete|archive",
  "statut_label": string,
  "fichier": {
    "nom_original": string,
    "nom_stocke": string,
    "extension": string,
    "taille": integer (bytes),
    "taille_formatee": string,
    "url_download": string
  },
  "etudiant": {
    "id": integer,
    "nom": string,
    "prenom": string,
    "matricule": string,
    "email": string,
    "faculte": string,
    "departement": string
  },
  "type_document": {
    "id": integer,
    "nom": string,
    "code": string,
    "description": string
  },
  "traitement": {
    "remarques": string|null,
    "date_traitement": string|null,
    "traite_par": object|null
  },
  "archivage": object|null,
  "created_at": string,
  "updated_at": string
}
```

### Pagination Response
```json
{
  "success": true,
  "data": [...],
  "meta": {
    "current_page": integer,
    "last_page": integer,
    "per_page": integer,
    "total": integer
  },
  "links": {
    "first": string,
    "last": string,
    "prev": string|null,
    "next": string|null
  }
}
```

---

## Error Responses

### Document Not Found (404)
```json
{
  "success": false,
  "message": "Document not found"
}
```

### Invalid Status (400)
```json
{
  "success": false,
  "message": "Invalid status. Valid statuses: en_attente, valide, rejete, archive"
}
```

### File Not Found (404)
```json
{
  "success": false,
  "message": "File not found on server"
}
```

---

## Best Practices

1. **Pagination**: Always use pagination for large datasets
   ```
   /api/v1/documents?per_page=100&page=1
   ```

2. **Error Handling**: Check `success` field in responses
   ```python
   if response.json()['success']:
       # Process data
   else:
       # Handle error
   ```

3. **File Downloads**: Use streaming for large files
   ```python
   response = requests.get(url, stream=True)
   ```

4. **Rate Limiting**: Be considerate with request frequency

5. **Filtering**: Use status filters to reduce data transfer
   ```
   /api/v1/documents/status/archive
   ```

---

## Testing

Test the API with curl:

```bash
# List all documents
curl http://your-domain.com/api/v1/documents

# Get specific document
curl http://your-domain.com/api/v1/documents/1

# Download file
curl http://your-domain.com/api/v1/documents/1/download -o file.pdf

# Filter by status
curl http://your-domain.com/api/v1/documents/status/valide

# Get archives
curl http://your-domain.com/api/v1/archives
```

---

## Support

For technical support or questions:
- **Email:** admin@uniburundi.bi
- **System:** Système d'Archivage - Université du Burundi

---

**API Version:** 1.0  
**Last Updated:** October 2024  
**Status:** Production Ready ✓
