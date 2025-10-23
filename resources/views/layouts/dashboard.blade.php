@extends('layouts.homePages')
@section('content')

    <style>
    /* Container spacing */
.container-xxl {
    padding: 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Tiles grid */
.tiles {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

/* Individual tile styling */
.tile {
    background: linear-gradient(145deg, #f6d365, #fda085);
    border-radius: 15px;
    padding: 20px;
    color: white;
    position: relative;
    overflow: hidden;
    cursor: pointer;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    animation: fadeInUp 0.6s ease forwards;
}

/* Tile hover effect */
.tile:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 25px rgba(0, 0, 0, 0.2);
}

/* Tile header layout */
.tile-header {
    display: flex;
    align-items: center;
    gap: 15px;
}

.tile-header i {
    font-size: 40px;
}

/* Tile text */
.header-font-size {
    font-size: 16px;
    font-weight: 500;
}

.tile h2 {
    margin: 5px 0 0;
    font-size: 28px;
    font-weight: bold;
}

/* Arrow link */
.tile a {
    position: absolute;
    bottom: 15px;
    right: 15px;
    color: white;
    transition: transform 0.3s ease;
}

.tile a:hover {
    transform: translateX(5px);
}

/* Fade in animation */
@keyframes fadeInUp {
    0% {
        opacity: 0;
        transform: translateY(20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <hr class="my-0">
        <div class="tiles">
            <article class="tile">
                <div class="tile-header">
                    <i class="ph-student-light"></i>
                    <h3>
                        <span class="header-font-size"># Total Cars Sold</span>
                        <span>
                            <h2>10.0</h2>
                        </span>
                    </h3>
                </div>
                <a href="#">
                    <span style="color: black;"></span>
                    <i class='bx bxs-right-arrow-circle' style="font-size: 30px;"></i>
                </a>
            </article>
            <article class="tile">
                <div class="tile-header">
                    <i class="ph-student-light"></i>
                    <h3>
                        <span class="header-font-size"># Total Revenue</span>
                        <span>
                            <h2>10.0</h2>
                        </span>
                    </h3>
                </div>
                <a href="#">
                    <span style="color: black;"></span>
                    <i class='bx bxs-right-arrow-circle' style="font-size: 30px;"></i>
                </a>
            </article>
            <article class="tile">
                <div class="tile-header">
                    <i class="ph-student-light"></i>
                    <h3>
                        <span class="header-font-size"># Brand</span>
                        <span>
                            <h2>10.0</h2>
                        </span>
                    </h3>
                </div>
                <a href="#">
                    <span style="color: black;"></span>
                    <i class='bx bxs-right-arrow-circle' style="font-size: 30px;"></i>
                </a>
            </article>
            <article class="tile">
                <div class="tile-header">
                    <i class="ph-student-light"></i>
                    <h3>
                        <span class="header-font-size"># Model Car</span>
                        <span>
                            <h2>10.0</h2>
                        </span>
                    </h3>
                </div>
                <a href="#">
                    <span style="color: black;"></span>
                    <i class='bx bxs-right-arrow-circle' style="font-size: 30px;"></i>
                </a>
            </article>
        </div>
    </div>

@endsection