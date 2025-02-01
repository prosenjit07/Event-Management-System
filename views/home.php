<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { padding-top: 2rem; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to the Application</h1>
        <div id="content" class="mt-4">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Data List</h5>
                            <div id="data-container"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        fetchData();
    });

    async function fetchData() {
        try {
            const response = await fetch('index.php?route=api/data');
            const data = await response.json();
            
            if (data.success) {
                displayData(data.data);
            } else {
                console.error('Error fetching data:', data.error);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    function displayData(items) {
        const container = document.getElementById('data-container');
        if (items.length === 0) {
            container.innerHTML = '<p>No items found.</p>';
            return;
        }

        const list = document.createElement('ul');
        list.className = 'list-group';
        
        items.forEach(item => {
            const li = document.createElement('li');
            li.className = 'list-group-item';
            li.textContent = `Item: ${item.name}`; // Adjust based on your data structure
            list.appendChild(li);
        });

        container.appendChild(list);
    }
    </script>
</body>
</html>