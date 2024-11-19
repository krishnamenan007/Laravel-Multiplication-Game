<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Fun Math Game</title>
    <script src="https://cdn.jsdelivr.net/npm/fabric@5.3.0/dist/fabric.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background-color: #f0f9ff;
            font-family: 'Comic Sans MS', cursive, sans-serif;
        }
        .timer {
            font-size: 1.5em;
            color: #ff6b6b;
            margin: 10px 0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        h1 {
            color: #ff6b6b;
            font-size: 2.5em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        h2 {
            color: #4ecdc4;
            font-size: 2em;
            animation: bounce 1s infinite;
        }
        #drawing-board {
    border: 4px solid #4ecdc4;
    border-radius: 15px;
    margin: 20px auto !important;
    background-color: white;
    display: block !important;
}

/* Add this new class */
.canvas-container {
    margin: 0 auto !important;
}
        .button {
            padding: 12px 25px;
            margin: 8px;
            cursor: pointer;
            background-color: #ff6b6b;
            color: white;
            border: none;
            border-radius: 25px;
            font-size: 1.1em;
            transition: transform 0.2s, background-color 0.2s;
        }
        .button:hover {
            transform: scale(1.05);
            background-color: #ff8787;
        }
        #keyboard-input {
            padding: 12px;
            font-size: 1.2em;
            border: 3px solid #4ecdc4;
            border-radius: 10px;
            width: 150px;
            text-align: center;
        }
        #result {
            margin-top: 20px;
            font-size: 1.5em;
            padding: 15px;
            border-radius: 10px;
        }
        .correct {
            background-color: #a8e6cf;
            animation: celebrate 0.5s ease-in-out;
        }
        .wrong {
            background-color: #ffaaa5;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        @keyframes celebrate {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .score-display {
            font-size: 1.2em;
            color: #666;
            margin: 10px 0;
        }
        .drawing-tools {
            margin: 10px 0;
        }
        .color-picker {
            margin: 10px;
        }
        .canvas-controls {
            margin-top:40px;
        }

        #result {
        margin-top: 20px;
        font-size: 1.5em;
        padding: 15px;
        border-radius: 10px;
        min-height: 60px; /* Prevents layout shift */
        transition: all 0.3s ease;
    }
    
    .correct, .wrong {
        padding: 15px;
        border-radius: 10px;
        margin: 10px 0;
    }
    
    .correct {
        background-color: #a8e6cf;
        animation: celebrate 0.5s ease-in-out;
    }
    
    .wrong {
        background-color: #ffaaa5;
    }
    #result {
    background-color: rgba(255, 255, 255, 0.8); /* Light background for visibility */
    /* ... existing styles ... */
}
    .loader {
    border: 8px solid #f3f3f3; /* Light grey */
    border-top: 8px solid #3498db; /* Blue */
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 10px auto; /* Center the loader */
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
        
    </style>
</head>
<body>
    <div class="container">
        <h1>🌟 Magic Math Game 🌟</h1>
        <div class="score-display">Score: <span id="score">0</span> ⭐</div>
        <div class="timer" id="timer">Time left: 15s</div> <!-- Timer display -->
        <h2>Can you solve {{ $num1 }} × {{ $num2 }}?</h2>

        <div class="input-section">
            <input type="number" id="keyboard-input" placeholder="Type here...">
            <button class="button" onclick="checkAnswer('keyboard')">✨ Submit Answer</button>
        </div>

        <div>
            <p>Or draw your answer below!</p>
            <div class="drawing-tools">
                <input type="color" id="brush-color" value="#000000" class="color-picker">
                <input type="range" id="brush-size" min="1" max="20" value="5">
            </div>
            <canvas id="drawing-board" width="400" height="200"></canvas>
            <div class="canvas-controls">
                <button class="button" onclick="clearCanvas()">🗑️ Clear</button>
                <button class="button" onclick="checkAnswer('drawing')">🎨 Submit Drawing</button>
            </div>
        </div>

        <div id="result"></div>
    </div>

    <script>
         var timer; // Timer variable
        var timeLeft = 150; // Time limit in seconds

        function startTimer() {
            timeLeft = 150; // Reset time left
            document.getElementById('timer').innerHTML = `Time left: ${timeLeft}`;
            timer = setInterval(function() {
                timeLeft--;
                document.getElementById('timer').innerHTML = `Time left: ${timeLeft}`;
                if (timeLeft <= 0) {
                    clearInterval(timer);
                    document.getElementById('result').innerHTML = "⏰ Time's up! Please try again! 💔";
                    document.querySelector('h2').innerHTML = `Can you solve ${data.num1} × ${data.num2}?`; 
                    document.getElementById('result').className = 'wrong';
                    
                }
            }, 100);
        }
        // Add this at the start of your script section
      
        // Add this event listener at the start of your script
// document.addEventListener('DOMContentLoaded', function() {

//     document.querySelectorAll('button').forEach(button => {
//         button.addEventListener('click', function(e) {
//             e.preventDefault();
//         });
//     });
// });

        // Initialize Fabric.js canvas
        var canvas = new fabric.Canvas('drawing-board', {
            isDrawingMode: true
        });
        canvas.freeDrawingBrush.width = 5;

        function clearCanvas() {
            canvas.clear();
        }

        function checkAnswer(method) {

            clearInterval(timer); // Stop the timer when an answer is submitted
            let scorePoints = 0; // Initialize score points

            if (timeLeft > 0) {
                scorePoints = Math.ceil((timeLeft / 50) * 100); // Score based on remaining time
                updateScore(scorePoints);
            }

            if (method === 'keyboard') {
                const answer = document.getElementById('keyboard-input').value;
                fetch('/game/check', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ answer: answer })
        })
        .then(response => response.json())
        .then(data => {
            const resultDiv = document.getElementById('result');
            if (data.correct) {
                resultDiv.innerHTML = '🎉 Correct! Well done! 🎉';
                resultDiv.className = 'correct';
                updateScore(1);
            } else {
                resultDiv.innerHTML = `❌ Not quite! The answer was ${data.correct_answer}. Try again! 💪`;
                resultDiv.className = 'wrong';
            }
            document.getElementById('keyboard-input').value = '';
             // Update the displayed numbers

            document.querySelector('h2').innerHTML = `Can you solve ${data.num1} × ${data.num2}?`; 
            clearCanvas();// Update the question
            startTimer();
        });
               

            } else if (method === 'drawing') {
                const resultDiv = document.getElementById('result');
                resultDiv.innerHTML = 'Recognizing your drawing... 🤔';
                resultDiv.innerHTML += '<div class="loader"></div>'; // Add this line
                 // Change the brush color to white before getting the image data
                // canvas.freeDrawingBrush.color = '#FFFFFF'; // Set brush color to white
            
                // Get the canvas image data
                const imageData = canvas.toDataURL('image/png');
                
                // Send to Python backend for processing
                fetch('http://127.0.0.1:5000/describe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ image: imageData })
                })
                .then(response => response.json())
                .then(data => {
                    document.querySelector('.loader').remove(); // Add this line
                    if (data.text) {
                        // Convert the extracted text to a number
                        const answer = parseFloat(data.text);
                        fetch('/game/check', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ answer: answer })
                    })
                    .then(response => response.json())
                    .then(data => {
                        const resultDiv = document.getElementById('result');
                        if (data.correct) {
                            resultDiv.innerHTML = '🎉 Correct! Well done! 🎉';
                            resultDiv.className = 'correct';
                            updateScore(1);
                        } else {
                            resultDiv.innerHTML = `❌ Not quite! The answer was ${data.correct_answer}. Try again! 💪`;
                            resultDiv.className = 'wrong';
                        }
                        // document.getElementById('keyboard-input').value = '';
                        document.querySelector('h2').innerHTML = `Can you solve ${data.num1} × ${data.num2}?`;
                        clearCanvas();
                        startTimer();
                    });
                        
                        // Check if the extracted answer is correct
                       
                    } else {
                        resultDiv.innerHTML = "Sorry, I couldn't recognize any numbers. Please try drawing it more clearly! ✏️";
                        resultDiv.className = 'wrong';
                    }
                    document.querySelector('h2').innerHTML = `Can you solve ${data.num1} × ${data.num2}?`;
                    clearCanvas(); // Update the question
                    startTimer();
                })
                
                .catch(error => {
                    resultDiv.innerHTML = "Oops! Something went wrong. Please try again! 🔄";
                    resultDiv.className = 'wrong';
                    // console.error('Error:', error);
                });
            }
        }





        function updateScore(points) {
    const scoreElement = document.getElementById('score');
    const currentScore = parseInt(scoreElement.textContent);
    scoreElement.textContent = currentScore + points;
}

        // Initialize canvas and drawing controls
        document.addEventListener('DOMContentLoaded', function() {
        
            document.querySelectorAll('button').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                });
            });

            // Add color and brush size controls
            document.getElementById('brush-color').addEventListener('change', function(e) {
                canvas.freeDrawingBrush.color = e.target.value;
            });

            document.getElementById('brush-size').addEventListener('change', function(e) {
                canvas.freeDrawingBrush.width = parseInt(e.target.value);
            });
        });
    </script>
</body>
</html>