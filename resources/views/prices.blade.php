<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora de Precios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light min-vh-100 d-flex flex-column justify-content-between py-4">

    <div class="container py-3">
        <header class="text-center mb-5">
            <h1 class="display-6 fw-bold text-dark mt-2">Calculadora de Precios</h1>
            <p class="text-muted col-md-8 mx-auto">
                Conversor de divisas y cálculo de descuentos.
            </p>
        </header>

        <div class="row g-4 justify-content-center">
            <div class="col-lg-6">
                <div class="card h-100 shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-bottom py-3 rounded-top-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                    <i class="bi bi-currency-exchange fs-5"></i>
                                </div>
                                <div>
                                    <h2 class="h5 mb-0 fw-bold text-dark">Conversión de Divisas</h2>
                                    <small class="text-muted">EUR, USD, JPY, INR y Protogemas</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="mb-3">
                                <label for="convAmount" class="form-label fw-semibold">Importe a convertir</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-cash"></i></span>
                                    <input type="number" class="form-control" id="convAmount" value="20" step="0.01" min="0">
                                </div>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-sm-6">
                                    <label for="convFrom" class="form-label fw-semibold">De:</label>
                                    <select class="form-select" id="convFrom">
                                        <option value="EUR" selected>Euros (EUR - €)</option>
                                        <option value="USD">Dólares (USD - $)</option>
                                        <option value="JPY">Yen japonés (JPY - ¥)</option>
                                        <option value="INR">Rupia india (INR - ₹)</option>
                                        <option value="PRIMO">Protogemas (✦)</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label for="convTo" class="form-label fw-semibold">A:</label>
                                    <select class="form-select" id="convTo">
                                        <option value="PRIMO" selected>Protogemas (✦)</option>
                                        <option value="EUR">Euros (EUR - €)</option>
                                        <option value="USD">Dólares (USD - $)</option>
                                        <option value="JPY">Yen japonés (JPY - ¥)</option>
                                        <option value="INR">Rupia india (INR - ₹)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="convRate" class="form-label fw-semibold">Tasa personalizada <span class="text-muted fw-normal">(opcional)</span></label>
                                <input type="number" class="form-control" id="convRate" step="0.0001" min="0.0001">
                            </div>
                        </div>

                        <div>
                            <button type="button" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm" onclick="convertCurrency()">
                                <i class="bi bi-calculator me-1"></i> Calcular Conversión
                            </button>

                            <div id="convResult" class="alert alert-success mt-3 mb-0" style="display: none;"></div>
                            <div id="convError" class="alert alert-danger mt-3 mb-0" style="display: none;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card h-100 shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-bottom py-3 rounded-top-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                <i class="bi bi-percent fs-5"></i>
                            </div>
                            <div>
                                <h2 class="h5 mb-0 fw-bold text-dark">Cálculo de Descuento</h2>
                                <small class="text-muted">Ahorro y precio final</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="mb-3">
                                <label for="discPrice" class="form-label fw-semibold">Precio original</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                    <input type="number" class="form-control" id="discPrice" value="100" step="0.01" min="0">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="discPercent" class="form-label fw-semibold">Porcentaje de descuento (%)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="discPercent" value="20" step="0.1" min="0" max="100">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <button type="button" class="btn btn-success w-100 py-2 fw-semibold shadow-sm" onclick="calculateDiscount()">
                                <i class="bi bi-tag-fill me-1"></i> Calcular Descuento
                            </button>

                            <div id="discResult" class="alert alert-success mt-3 mb-0" style="display: none;"></div>
                            <div id="discError" class="alert alert-danger mt-3 mb-0" style="display: none;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        const currencySymbols = {
            'USD': '$',
            'EUR': '€',
            'JPY': '¥',
            'INR': '₹',
            'PRIMO': '✦'
        };

        async function convertCurrency() {
            const amount = document.getElementById('convAmount').value;
            const from = document.getElementById('convFrom').value;
            const to = document.getElementById('convTo').value;
            const rate = document.getElementById('convRate').value;
            const resBox = document.getElementById('convResult');
            const errBox = document.getElementById('convError');

            resBox.style.display = 'none';
            errBox.style.display = 'none';

            try {
                let url = `/prices/convert?amount=${encodeURIComponent(amount)}&from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}`;
                if (rate) url += `&rate=${encodeURIComponent(rate)}`;

                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();

                if (!res.ok) throw new Error(data.message || 'Error en la conversión');

                const sym = currencySymbols[data.to] || '';
                let extraHtml = '';

                if (data.to === 'PRIMO' && typeof data.wishes !== 'undefined') {
                    extraHtml = `
                        <div class="mt-2 pt-2 border-top border-success-subtle">
                            <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill">
                                <i class="bi bi-stars"></i> Equivale a <strong>${data.wishes} Deseos</strong>
                            </span>
                        </div>
                    `;
                }

                resBox.innerHTML = `
                    <div class="small text-muted mb-1">${data.original_amount} ${data.from} equivalen a:</div>
                    <div class="h3 fw-bold mb-0 text-success">${data.converted_amount} ${sym} <span class="fs-6 fw-normal text-muted">(${data.to})</span></div>
                    ${extraHtml}
                `;
                resBox.style.display = 'block';
            } catch (err) {
                errBox.innerText = err.message;
                errBox.style.display = 'block';
            }
        }

        async function calculateDiscount() {
            const price = document.getElementById('discPrice').value;
            const percent = document.getElementById('discPercent').value;
            const resBox = document.getElementById('discResult');
            const errBox = document.getElementById('discError');

            resBox.style.display = 'none';
            errBox.style.display = 'none';

            try {
                const url = `/prices/discount?price=${encodeURIComponent(price)}&discount_percentage=${encodeURIComponent(percent)}`;
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();

                if (!res.ok) throw new Error(data.message || 'Error al calcular descuento');

                resBox.innerHTML = `
                    <div class="small text-muted mb-1">
                        Ahorro del <strong>${data.discount_percentage}%</strong>: <span class="text-danger fw-bold">-${data.discount_amount}</span> (de ${data.original_price})
                    </div>
                    <div class="h3 fw-bold mb-0 text-success">${data.final_price} <span class="fs-6 fw-normal text-muted">precio final</span></div>
                `;
                resBox.style.display = 'block';
            } catch (err) {
                errBox.innerText = err.message;
                errBox.style.display = 'block';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            convertCurrency();
        });
    </script>
</body>
</html>
