<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Redirecting to Cashfree...</title>
    <script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>
</head>
<body>
    <p>Redirecting to secure payment page, please wait...</p>

    <script>
        const cashfree = Cashfree({ mode: "{{ $mode }}" }); // "sandbox" or "production"

        cashfree.checkout({
            paymentSessionId: "{{ $paymentSessionId }}",
            redirectTarget: "_self",
        });
    </script>
</body>
</html>