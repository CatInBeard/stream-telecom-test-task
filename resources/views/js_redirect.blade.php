<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting</title>
</head>
<body>
<p>You will be redirected shortly. If the redirect does not occur, please click <a href="{{ $redirectUrl }}">here</a></p>

<script>

    function redirect() {
        window.location.href = "{{ $redirectUrl }}";
    }

    getCanvasFingerprint = () => {
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');

        canvas.width = 300;
        canvas.height = 150;

        context.fillStyle = '#e0e0e0';
        context.fillRect(0, 0, canvas.width, canvas.height);

        context.textBaseline = 'top';
        context.font = '24px Verdana';
        context.fillStyle = '#333333';
        context.fillText('Fingerprinting Test', 15, 15);

        context.font = '20px Georgia';
        context.fillStyle = '#ff5733';
        context.fillText('Unique Identifier', 15, 50);

        context.font = '18px Tahoma';
        context.fillStyle = '#33c1ff';
        context.fillText('Canvas Fingerprint', 15, 80);

        context.fillStyle = '#ffcc00';
        context.fillRect(200, 20, 70, 40);

        context.fillStyle = '#00cc66';
        context.beginPath();
        context.arc(100, 120, 30, 0, Math.PI * 2, true);
        context.fill();

        context.fillStyle = '#ff33cc';
        context.beginPath();
        context.moveTo(150, 100);
        context.lineTo(180, 130);
        context.lineTo(120, 130);
        context.closePath();
        context.fill();

        return canvas.toDataURL();
    }


    getFonts = () => {
        const fonts = [];
        const testString = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        const testCanvas = document.createElement('canvas');
        const testContext = testCanvas.getContext('2d');
        testContext.font = '72px monospace';
        const baselineWidth = testContext.measureText(testString).width;

        const fontList = [
            'Arial', 'Verdana', 'Courier New', 'Georgia', 'Times New Roman',
            'Trebuchet MS', 'Comic Sans MS', 'Impact', 'Lucida Console',
            'Tahoma', 'Arial Black', 'Palatino Linotype', 'Book Antiqua',
            'Arial Narrow', 'Segoe UI', 'Helvetica', 'Frank Ruhl Libre',
            'Fira Sans', 'Roboto', 'Open Sans', 'Droid Sans',
            'Noto Sans', 'Montserrat', 'Lato', 'Source Sans Pro',
            'PT Sans', 'Raleway', 'Oswald', 'Merriweather',
            'Ubuntu', 'Playfair Display', 'Baskerville', 'Georgia Pro'
        ];

        fontList.forEach(font => {
            testContext.font = `72px ${font}`;
            const width = testContext.measureText(testString).width;
            if (width !== baselineWidth) {
                fonts.push(font);
            }
        });

        return fonts;
    }

    collectData = () => {
        const data = {
            userAgent: navigator.userAgent,
            language: navigator.language,
            platform: navigator.platform,
            screenResolution: `${screen.width}x${screen.height}`,
            colorDepth: screen.colorDepth,
            timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
            plugins: Array.from(navigator.plugins).map(plugin => plugin.name),
            cookiesEnabled: navigator.cookieEnabled,
            hardwareConcurrency: navigator.hardwareConcurrency,
            deviceMemory: navigator.deviceMemory,
            onlineStatus: navigator.onLine,
            viewportSize: `${window.innerWidth}x${window.innerHeight}`,
            canvasFingerprint: getCanvasFingerprint(),
            installedFonts: getFonts(),
            browserName: navigator.appName,
            browserVersion: navigator.appVersion,
            windowSize: {
                width: window.outerWidth,
                height: window.outerHeight,
                innerWidth: window.innerWidth,
                innerHeight: window.innerHeight,
            },
            localStorageAvailable: typeof(Storage) !== "undefined",
            sessionStorageAvailable: typeof(sessionStorage) !== "undefined",
            cssProperties: {
                flexbox: CSS.supports('display', 'flex'),
                grid: CSS.supports('display', 'grid'),
            }
        };

        return JSON.stringify(data, null, 2);
    };

    function sendPostRequest(data) {
        fetch('{{ route('short-links-additional-data.store', ['id' => $visitId]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: data
        })
            .then(response => {
                redirect();
            })
            .then(data => {
                redirect();
            })
            .catch((error) => {
                redirect();
            });
    }

    function onLoad() {
        const data = collectData();
        sendPostRequest(data);
    }

    window.onload = onLoad;
</script>
</body>
</html>

