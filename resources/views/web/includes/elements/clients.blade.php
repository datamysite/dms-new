<div class="section clients-grid-section">
    <div class="container">

        <!-- Section Header -->
        <div class="clients-head">
            <div class="clients-head-left">
                <span class="clients-eyebrow">Our Clients</span>
                <h2 class="clients-title">Brands we work with</h2>
            </div>
            <p class="clients-desc">
                At DataMySite, we are proud to partner with a diverse range of clients across various industries, from startups to established global brands.
            </p>
        </div><!-- End Section Header -->

        <!-- Logo Grid -->
        <div class="clients-grid">
            @php
                // Image number => brand name (ordered by brand size, biggest first)
                $clientLogos = [
                    // Global / major brands
                    38 => 'Nike',           12 => 'Intel',          35 => 'Whole Foods',    31 => 'e&',
                    1  => 'Dubai Duty Free', 4 => 'DAMAC',          17 => 'MakeMyTrip',
                    // Large regional brands
                    6  => 'Lenskart',       16 => 'Cerruti',        3  => 'Danube Properties', 2 => 'Jetour',
                    39 => 'Géant',          36 => "Haldiram's",     37 => 'GMG',
                    // Well-known UAE brands
                    25 => 'Al Rawabi',      28 => 'Al Ain Farms',   20 => 'SIVVI',          40 => 'Aswaaq',
                    7  => 'Logicom Group',  5  => 'ILG',            24 => 'MARQ Dubai',
                    // Established regional businesses
                    26 => 'Hayata',         27 => 'Cavo',           13 => 'Elite Group Holding', 14 => 'BNW Developments',
                    8  => 'Arabian Hills',  19 => 'Tranquil Developers', 21 => 'Prime Estate',
                    // Growing businesses
                    18 => 'Oscar Luxury Auto', 15 => 'AYS Developers', 32 => 'USI Consultancy', 34 => 'First Super Car Rental',
                    33 => 'Royalty Tourism', 29 => 'Darcey Flowers', 30 => 'Peace Homes Development',
                    // Startups & boutique brands
                    9  => "D's Damat",      10 => 'Metamind Inspire', 11 => 'Ask Kubeir',   22 => 'Lasa Ona',
                    23 => 'Just Love UAE',  41 => 'Royal',
                ];
            @endphp

            @foreach($clientLogos as $i => $brand)
                <div class="client-cell">
                    <a href="">
                        <img src="{{ URL::to('/public/assets/img/clients-final/'.$i.'.png') }}" loading="lazy" alt="{{ $brand }} logo">
                    </a>
                </div>
            @endforeach
        </div><!-- End Logo Grid -->

    </div>
</div>

<style>
   

    /* ---------- Header ---------- */
    .clients-grid-section .clients-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 40px;
        margin-bottom: 40px;
    }
    .clients-grid-section .clients-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 3px;
        text-transform: uppercase;
        color: #e04a43;
        margin-bottom: 8px;
    }
    .clients-grid-section .clients-eyebrow::before {
        content: "";
        width: 22px;
        height: 2px;
        background: #e04a43;
    }
    .clients-grid-section .clients-title {
        font-size: 34px;
        font-weight: 800;
        line-height: 1.2;
        color: #111a14;
        margin: 0;
        letter-spacing: -0.5px;
    }
    .clients-grid-section .clients-desc {
        max-width: 430px;
        font-size: 15px;
        line-height: 1.6;
        color: #6b6b6b;
        margin: 0;
    }

    /* ---------- Grid ---------- */
    .clients-grid-section .clients-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        border-top: 1px solid #ececec;
        border-left: 1px solid #ececec;
    }
    .clients-grid-section .client-cell {
        border-right: 1px solid #ececec;
        border-bottom: 1px solid #ececec;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background-color .3s ease;
    }
    .clients-grid-section .client-cell a {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
    }
    .clients-grid-section .client-cell img {
        width: 110%;
        height: 110%;
        max-height: 85px;
        object-fit: contain;
        filter: grayscale(100%);
        opacity: .7;
        transition: filter .3s ease, opacity .3s ease, transform .3s ease;
    }
    .clients-grid-section .client-cell:hover {
        background-color: #fafafa;
    }
    .clients-grid-section .client-cell:hover img {
        filter: grayscale(0%);
        opacity: 1;
        transform: scale(1.05);
    }

    /* ---------- Responsive ---------- */
    @media (max-width: 1199px) {
        .clients-grid-section .clients-grid { grid-template-columns: repeat(5, 1fr); }
    }
    @media (max-width: 991px) {
        .clients-grid-section { padding: 60px 0; }
        .clients-grid-section .clients-head { flex-direction: column; gap: 16px; }
        .clients-grid-section .clients-desc { max-width: 100%; }
        .clients-grid-section .clients-grid { grid-template-columns: repeat(4, 1fr); }
    }
    @media (max-width: 767px) {
        .clients-grid-section .clients-title { font-size: 28px; }
        .clients-grid-section .clients-grid { grid-template-columns: repeat(3, 1fr); }
        .clients-grid-section .client-cell { height: 90px; }
        .clients-grid-section .client-cell img { max-height: 70px; }
    }
    @media (max-width: 480px) {
        .clients-grid-section .clients-grid { grid-template-columns: repeat(3, 1fr); }
    }
</style>