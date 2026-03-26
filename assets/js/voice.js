 document.addEventListener('DOMContentLoaded', function() {
    const voiceBtn = document.getElementById('voiceSearchBtn');
    if (!voiceBtn) return;

    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SpeechRecognition) {
        voiceBtn.style.display = 'none';
        console.log('Voice search not supported in this browser');
        return;
    }

    voiceBtn.addEventListener('click', function() {
        const recognition = new SpeechRecognition();
        recognition.lang = 'en-US';  // English (more reliable)
        recognition.interimResults = false;
        recognition.maxAlternatives = 1;

        voiceBtn.style.backgroundColor = '#ff4444';
        voiceBtn.textContent = '🔴 Listening...';

        recognition.onresult = function(event) {
            let transcript = event.results[0][0].transcript.trim().toLowerCase();
            console.log('You said:', transcript);

            // Map common Tamil words spoken in English accent
            const tamilMap = {
                'thakkali': 'tomato',
                'thakali': 'tomato',
                'kathirikai': 'brinjal',
                'kathirikkai': 'brinjal',
                'brinjal': 'brinjal',
                'vendakai': 'ladiesfinger',
                'vendakkai': 'ladiesfinger',
                'ladiesfinger': 'ladiesfinger',
                'milagai': 'chilli',
                'milakai': 'chilli',
                'chilli': 'chilli',
                'vaazhaipazham': 'banana',
                'banana': 'banana',
                'maambazham': 'mango',
                'mango': 'mango',
                'apple': 'apple',
                'orange': 'orange',
                'ragi': 'ragi',
                'bajra': 'bajra',
                'tomato': 'tomato'
            };

            let searchTerm = transcript;
            for (let [spoken, english] of Object.entries(tamilMap)) {
                if (transcript.includes(spoken)) {
                    searchTerm = english;
                    break;
                }
            }

            window.location.href = 'products.php?search=' + encodeURIComponent(searchTerm);
        };

        recognition.onerror = function(event) {
            console.error('Voice error:', event.error);
            alert('Voice recognition error: ' + event.error + '. Please try again.');
            voiceBtn.style.backgroundColor = '';
            voiceBtn.textContent = '🎤';
        };

        recognition.onend = function() {
            voiceBtn.style.backgroundColor = '';
            voiceBtn.textContent = '🎤';
        };

        recognition.start();
    });
});