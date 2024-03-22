<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Screen Recorder</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <button id="startBtn" onclick="startRecording()">Start Recording</button>
    <button id="stopBtn" onclick="stopRecording()" disabled>Stop Recording</button>
    <script>
        let recorder;
        let chunks = [];

        async function startRecording() {
            try {
                const screenStream = await navigator.mediaDevices.getDisplayMedia({
                    video: true
                });
                const audioStream = await navigator.mediaDevices.getUserMedia({
                    audio: true
                });
                const tracks = [...screenStream.getTracks(), ...audioStream.getAudioTracks()];
                const stream = new MediaStream(tracks);

                recorder = new MediaRecorder(stream);
                chunks = [];

                recorder.ondataavailable = e => chunks.push(e.data);
                recorder.onstop = uploadVideo;

                recorder.start();
                document.getElementById('startBtn').disabled = true;
                document.getElementById('stopBtn').disabled = false;
            } catch (error) {
                console.error('Error capturing screen or audio:', error);
            }
        }

        function stopRecording() {
            recorder.stop();
            document.getElementById('startBtn').disabled = false;
            document.getElementById('stopBtn').disabled = true;
        }

        function uploadVideo() {
            const blob = new Blob(chunks, {
                'type': 'video/mp4'
            });
            let formData = new FormData();
            formData.append('video', blob, 'screen-recording.mp4');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('/upload-video', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: formData,
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(data => console.log(data))
                .catch(error => console.error('Error uploading video:', error));
        }
    </script>
</body>

</html>
