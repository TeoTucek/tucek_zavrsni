<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OOP PHP - Cheat Sheet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
            body { font-size: 11px; }
            .container { max-width: 100%; }
        }
        .code-box {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 13px;
        }
        .code-box .keyword { color: #569cd6; }
        .code-box .string { color: #ce9178; }
        .code-box .comment { color: #6a9955; }
        .code-box .variable { color: #9cdcfe; }
        .code-box .function { color: #dcdcaa; }
        .section-title {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body class="py-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <a href="index.php" class="btn btn-outline-secondary">&larr; Natrag</a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="bi bi-printer"></i> Ispiši
            </button>
        </div>

        <div class="text-center mb-4">
            <h1>OOP PHP - Cheat Sheet</h1>
            <p class="text-muted">Brzi podsjetnik za objektno orijentirano programiranje</p>
        </div>

        <div class="row">
            <!-- KOLONA 1 -->
            <div class="col-md-6">
                <!-- KLASA I OBJEKT -->
                <h4 class="section-title">1. Klasa i objekt</h4>
                <div class="code-box mb-4">
<span class="comment">// Definicija klase</span>
<span class="keyword">class</span> <span class="function">Automobil</span> {
    <span class="keyword">public</span> <span class="variable">$marka</span>;
    <span class="keyword">public</span> <span class="variable">$model</span>;

    <span class="keyword">public function</span> <span class="function">info</span>() {
        <span class="keyword">return</span> <span class="variable">$this</span>->marka;
    }
}

<span class="comment">// Kreiranje objekta</span>
<span class="variable">$auto</span> = <span class="keyword">new</span> <span class="function">Automobil</span>();
<span class="variable">$auto</span>->marka = <span class="string">"Fiat"</span>;
<span class="keyword">echo</span> <span class="variable">$auto</span>-><span class="function">info</span>();
                </div>

                <div class="alert alert-light border">
                    <strong>Pojmovi:</strong>
                    <ul class="mb-0 small">
                        <li><code>class</code> - definira nacrt (blueprint)</li>
                        <li><code>new</code> - kreira objekt iz klase</li>
                        <li><code>$this</code> - referenca na trenutni objekt</li>
                        <li><code>-></code> - pristup svojstvima/metodama</li>
                    </ul>
                </div>

                <!-- KONSTRUKTOR -->
                <h4 class="section-title">2. Konstruktor</h4>
                <div class="code-box mb-4">
<span class="keyword">class</span> <span class="function">Automobil</span> {
    <span class="keyword">public</span> <span class="variable">$marka</span>;

    <span class="comment">// Poziva se automatski kod "new"</span>
    <span class="keyword">public function</span> <span class="function">__construct</span>(<span class="variable">$marka</span>) {
        <span class="variable">$this</span>->marka = <span class="variable">$marka</span>;
    }
}

<span class="comment">// Sada je jednostavnije:</span>
<span class="variable">$auto</span> = <span class="keyword">new</span> <span class="function">Automobil</span>(<span class="string">"Fiat"</span>);
                </div>

                <!-- MODIFIKATORI -->
                <h4 class="section-title">3. Modifikatori pristupa</h4>
                <table class="table table-sm table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Modifikator</th>
                            <th>Klasa</th>
                            <th>Djeca</th>
                            <th>Izvana</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-success">
                            <td><code>public</code></td>
                            <td>Da</td>
                            <td>Da</td>
                            <td>Da</td>
                        </tr>
                        <tr class="table-warning">
                            <td><code>protected</code></td>
                            <td>Da</td>
                            <td>Da</td>
                            <td>Ne</td>
                        </tr>
                        <tr class="table-danger">
                            <td><code>private</code></td>
                            <td>Da</td>
                            <td>Ne</td>
                            <td>Ne</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- KOLONA 2 -->
            <div class="col-md-6">
                <!-- PRIVATE PRIMJER -->
                <h4 class="section-title">4. Private u praksi</h4>
                <div class="code-box mb-4">
<span class="keyword">class</span> <span class="function">Korisnik</span> {
    <span class="keyword">private</span> <span class="variable">$lozinka</span>;

    <span class="keyword">public function</span> <span class="function">setLozinka</span>(<span class="variable">$nova</span>) {
        <span class="keyword">if</span> (<span class="function">strlen</span>(<span class="variable">$nova</span>) >= 6) {
            <span class="variable">$this</span>->lozinka = <span class="variable">$nova</span>;
            <span class="keyword">return true</span>;
        }
        <span class="keyword">return false</span>;
    }

    <span class="keyword">public function</span> <span class="function">provjeri</span>(<span class="variable">$unos</span>) {
        <span class="keyword">return</span> <span class="variable">$this</span>->lozinka === <span class="variable">$unos</span>;
    }
}
                </div>

                <!-- NASLJEĐIVANJE -->
                <h4 class="section-title">5. Nasljeđivanje (extends)</h4>
                <div class="code-box mb-4">
<span class="comment">// Bazna klasa</span>
<span class="keyword">class</span> <span class="function">Vozilo</span> {
    <span class="keyword">protected</span> <span class="variable">$marka</span>;

    <span class="keyword">public function</span> <span class="function">__construct</span>(<span class="variable">$marka</span>) {
        <span class="variable">$this</span>->marka = <span class="variable">$marka</span>;
    }

    <span class="keyword">public function</span> <span class="function">info</span>() {
        <span class="keyword">return</span> <span class="variable">$this</span>->marka;
    }
}

<span class="comment">// Naslijeđena klasa</span>
<span class="keyword">class</span> <span class="function">Automobil</span> <span class="keyword">extends</span> <span class="function">Vozilo</span> {
    <span class="keyword">private</span> <span class="variable">$vrata</span>;

    <span class="keyword">public function</span> <span class="function">__construct</span>(<span class="variable">$marka</span>, <span class="variable">$vrata</span>) {
        <span class="comment">// Pozovi roditeljev konstruktor</span>
        <span class="keyword">parent</span>::<span class="function">__construct</span>(<span class="variable">$marka</span>);
        <span class="variable">$this</span>->vrata = <span class="variable">$vrata</span>;
    }

    <span class="comment">// Override metode</span>
    <span class="keyword">public function</span> <span class="function">info</span>() {
        <span class="keyword">return parent</span>::<span class="function">info</span>() .
               <span class="string">" ("</span> . <span class="variable">$this</span>->vrata . <span class="string">" vrata)"</span>;
    }
}

<span class="variable">$auto</span> = <span class="keyword">new</span> <span class="function">Automobil</span>(<span class="string">"Fiat"</span>, 5);
<span class="keyword">echo</span> <span class="variable">$auto</span>-><span class="function">info</span>(); <span class="comment">// Fiat (5 vrata)</span>
                </div>

                <div class="alert alert-light border">
                    <strong>Ključne riječi:</strong>
                    <ul class="mb-0 small">
                        <li><code>extends</code> - nasljeđuje klasu</li>
                        <li><code>parent::</code> - poziva roditeljevu metodu</li>
                        <li><code>protected</code> - djeca mogu pristupiti</li>
                        <li><code>override</code> - nadpisivanje metode</li>
                    </ul>
                </div>

                <!-- ČESTE GREŠKE -->
                <h4 class="section-title">6. Česte greške</h4>
                <table class="table table-sm">
                    <tr>
                        <td class="text-danger">$marka</td>
                        <td class="text-success">$this->marka</td>
                        <td><small>Unutar klase</small></td>
                    </tr>
                    <tr>
                        <td class="text-danger">$auto.marka</td>
                        <td class="text-success">$auto->marka</td>
                        <td><small>PHP koristi -></small></td>
                    </tr>
                    <tr>
                        <td class="text-danger">new Automobil</td>
                        <td class="text-success">new Automobil()</td>
                        <td><small>Zagrade!</small></td>
                    </tr>
                </table>
            </div>
        </div>

        <hr>

        <!-- VIZUALNI DIJAGRAM -->
        <h4 class="section-title">Vizualni prikaz nasljeđivanja</h4>
        <div class="bg-light p-3 rounded text-center">
            <pre style="display: inline-block; text-align: left;">
         ┌─────────────────┐
         │     Vozilo      │  RODITELJ (parent)
         │─────────────────│
         │ # marka         │  # = protected
         │ # model         │
         │─────────────────│
         │ + info()        │  + = public
         └────────┬────────┘
                  │ extends
         ┌────────┴────────┐
         │                 │
    ┌────┴────┐      ┌─────┴─────┐
    │Automobil│      │ Motocikl  │  DJECA (children)
    │─────────│      │───────────│
    │ - vrata │      │ - ccm     │  - = private
    └─────────┘      └───────────┘
            </pre>
        </div>

        <div class="text-center mt-4 no-print">
            <a href="index.php" class="btn btn-secondary">&larr; Natrag na početnu</a>
        </div>
    </div>
</body>
</html>
