{{-- resources/views/student/browse-problems/components/map-view.blade.php --}}
{{-- component untuk menampilkan map view dengan leaflet --}}

<div x-data="mapView()" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative">
    {{-- map container --}}
    <div id="problems-map" class="w-full h-[600px] relative"></div>

    {{-- map controls overlay - moved to top right --}}
    <div class="absolute top-4 right-4 z-[900] space-y-2">
        {{-- filter toggle --}}
        <button @click="showFilters = !showFilters"
                class="bg-white px-4 py-2 rounded-lg shadow-lg border border-gray-200 hover:bg-gray-50 transition-colors flex items-center space-x-2">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
            </svg>
            <span class="text-sm font-medium text-gray-700">Filter</span>
        </button>
        
        {{-- filter panel --}}
        <div x-show="showFilters" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-lg shadow-lg border border-gray-200 p-4 w-64"
             style="display: none;">
            <h3 class="font-semibold text-gray-900 mb-3">Filter Peta</h3>
            
            <div class="space-y-3">
                {{-- difficulty filter --}}
                <div>
                    <label class="text-xs font-medium text-gray-700 mb-1 block">Tingkat Kesulitan</label>
                    <select @change="filterMarkers()" x-model="filters.difficulty" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua</option>
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>
                
                {{-- urgent filter --}}
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" @change="filterMarkers()" x-model="filters.urgentOnly" class="rounded text-blue-600 focus:ring-2 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Hanya Mendesak</span>
                    </label>
                </div>
                
                {{-- featured filter --}}
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" @change="filterMarkers()" x-model="filters.featuredOnly" class="rounded text-blue-600 focus:ring-2 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Hanya Unggulan</span>
                    </label>
                </div>
                
                {{-- reset button --}}
                <button @click="resetMapFilters()" class="w-full px-3 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition-colors">
                    Reset Filter
                </button>
            </div>
        </div>
    </div>
    
    {{-- legend --}}
    <div class="absolute bottom-4 left-4 z-[900] bg-white rounded-lg shadow-lg border border-gray-200 p-3">
        <h4 class="text-xs font-semibold text-gray-900 mb-2">Legenda</h4>
        <div class="space-y-1.5">
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"
                          fill="#eab308" stroke="white" stroke-width="1"/>
                </svg>
                <span class="text-xs text-gray-600">Unggulan</span>
            </div>
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"
                          fill="#ef4444" stroke="white" stroke-width="1"/>
                </svg>
                <span class="text-xs text-gray-600">Mendesak</span>
            </div>
        </div>
    </div>

    {{-- Loading indicator --}}
    <div x-show="!map" class="absolute inset-0 flex items-center justify-center bg-gray-100 z-[800]">
        <div class="text-center">
            <svg class="animate-spin h-12 w-12 text-blue-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-600">Memuat peta...</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
function mapView() {
    return {
        map: null,
        markers: [],
        markerCluster: null,
        showFilters: false,
        filters: {
            difficulty: '',
            urgentOnly: false,
            featuredOnly: false
        },
        problems: @json($mapProblems ?? []),

        init() {
            console.log('Map view component initialized');
            console.log('Problems received:', this.problems);
            console.log('Problems type:', typeof this.problems);
            console.log('Problems is array:', Array.isArray(this.problems));
            console.log('Problems count:', this.problems?.length || 'N/A');
            console.log('Leaflet available:', typeof L !== 'undefined');

            // wait for Leaflet to be ready
            if (typeof L === 'undefined') {
                console.error('Leaflet library not loaded!');
                return;
            }

            // DON'T initialize map automatically - wait for map view to be activated
            // listen untuk map view activated event
            window.addEventListener('mapViewActivated', () => {
                console.log('Map view activated event received');
                if (this.map) {
                    // map already exists, just refresh size
                    setTimeout(() => {
                        this.map.invalidateSize();
                        console.log('Map size invalidated');
                    }, 100);
                } else {
                    // map doesn't exist yet, initialize now
                    console.log('Map not initialized, initializing now...');
                    setTimeout(() => {
                        try {
                            this.initializeMap();
                            this.loadMarkers();
                            console.log('Map initialization completed');
                        } catch (error) {
                            console.error('Error initializing map:', error);
                        }
                    }, 100);
                }
            });
        },
        
        initializeMap() {
            console.log('initializeMap called');

            // check if container exists and is visible
            const container = document.getElementById('problems-map');
            if (!container) {
                console.error('Map container not found!');
                return;
            }

            const mapView = document.getElementById('map-view');
            if (mapView && mapView.classList.contains('hidden')) {
                console.log('Map view is hidden, skipping initialization');
                return;
            }

            console.log('Container exists and is visible, creating map...');

            // inisialisasi map
            this.map = L.map('problems-map', {
                center: [-2.5, 118], // koordinat tengah Indonesia
                zoom: 5,
                zoomControl: true,
                scrollWheelZoom: true
            });

            console.log('Map object created:', this.map);

            // tambahkan tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(this.map);

            console.log('Tile layer added');

            // inisialisasi marker cluster dengan custom icon
            this.markerCluster = L.markerClusterGroup({
                maxClusterRadius: 50,
                spiderfyOnMaxZoom: true,
                showCoverageOnHover: false,
                zoomToBoundsOnClick: true,
                iconCreateFunction: function(cluster) {
                    const count = cluster.getChildCount();
                    return L.divIcon({
                        html: `<div class="cluster-icon">
                                  <svg viewBox="0 0 40 40" class="w-10 h-10">
                                      <rect x="5" y="5" width="30" height="30" rx="4" fill="#3b82f6" stroke="white" stroke-width="2"/>
                                      <text x="20" y="25" text-anchor="middle" fill="white" font-size="14" font-weight="bold">${count}</text>
                                  </svg>
                               </div>`,
                        className: 'custom-cluster-icon',
                        iconSize: L.point(40, 40)
                    });
                }
            });

            this.map.addLayer(this.markerCluster);
            console.log('Marker cluster added');
        },
        
        loadMarkers() {
            console.log('loadMarkers called');

            if (!this.markerCluster) {
                console.error('Marker cluster not initialized!');
                return;
            }

            // bersihkan markers existing
            this.markerCluster.clearLayers();
            this.markers = [];

            // check if problems is array
            if (!Array.isArray(this.problems)) {
                console.error('Problems is not an array:', typeof this.problems);
                return;
            }

            console.log('Total problems to display on map:', this.problems.length);

            // tambahkan marker untuk setiap problem
            let successCount = 0;
            this.problems.forEach((problem, index) => {
                try {
                    const marker = this.createMarker(problem);
                    if (marker) {
                        this.markers.push({ marker, problem });
                        this.markerCluster.addLayer(marker);
                        successCount++;
                    }
                } catch (error) {
                    console.error(`Error creating marker for problem ${index}:`, error);
                }
            });

            console.log(`Successfully created ${successCount} markers out of ${this.problems.length} problems`);

            // fit bounds jika ada markers
            if (this.markers.length > 0) {
                try {
                    const group = new L.featureGroup(this.markers.map(m => m.marker));
                    this.map.fitBounds(group.getBounds().pad(0.1));
                    console.log('Map bounds fitted to markers');
                } catch (error) {
                    console.error('Error fitting bounds:', error);
                }
            } else {
                console.warn('No markers to display');
            }
        },
        
        createMarker(problem) {
            // gunakan koordinat dari province
            const coords = this.getProvinceCoordinates(problem);
            if (!coords) return null;

            const lat = problem.latitude || coords.lat;
            const lng = problem.longitude || coords.lng;

            // tentukan warna marker berdasarkan status
            const markerColor = this.getMarkerColor(problem);

            // buat custom icon - minimalist location pin
            const icon = L.divIcon({
                className: 'custom-marker',
                html: `
                    <svg class="w-7 h-7 drop-shadow-md" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"
                              fill="${markerColor}"
                              stroke="white"
                              stroke-width="1"/>
                    </svg>
                `,
                iconSize: [28, 28],
                iconAnchor: [14, 28],
                popupAnchor: [0, -28]
            });
            
            const marker = L.marker([lat, lng], { icon });
            
            // tambahkan popup
            const popupContent = this.createPopupContent(problem);
            marker.bindPopup(popupContent, {
                maxWidth: 450,
                minWidth: 350,
                className: 'custom-popup'
            });
            
            // event handlers
            marker.on('click', () => {
                this.onMarkerClick(problem);
            });
            
            return marker;
        },
        
        createPopupContent(problem) {
            const daysLeft = this.calculateDaysLeft(problem.application_deadline);
            const institutionName = problem.institution?.name || 'Instansi';
            const regencyName = problem.regency?.name || problem.location_regency || 'Kabupaten/Kota';
            const provinceName = problem.province?.name || problem.location_province || 'Provinsi';

            return `
                <div class="p-3">
                    <h3 class="font-bold text-gray-900 mb-3 text-base leading-tight">${problem.title || 'Judul Proyek'}</h3>

                    <div class="grid grid-cols-2 gap-x-4 gap-y-2 mb-3 text-xs text-gray-600">
                        <div class="flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span class="truncate">${institutionName}</span>
                        </div>
                        <div class="flex items-center ${daysLeft <= 7 ? 'text-red-600 font-semibold' : ''}">
                            <svg class="w-3.5 h-3.5 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>${daysLeft} hari lagi</span>
                        </div>
                        <div class="flex items-center col-span-2">
                            <svg class="w-3.5 h-3.5 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            </svg>
                            <span class="truncate">${regencyName}, ${provinceName}</span>
                        </div>
                    </div>

                    <a href="/student/browse-problems/${problem.id}"
                       class="block w-full px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-blue-600 text-white text-sm font-bold text-center rounded-lg hover:from-indigo-700 hover:to-blue-700 transition-all shadow-md hover:shadow-lg">
                        Lihat Detail Proyek
                    </a>
                </div>
            `;
        },
        
        getMarkerColor(problem) {
            if (problem.is_urgent) return '#ef4444'; // red-500
            if (problem.is_featured) return '#eab308'; // yellow-500
            return '#3b82f6'; // blue-500
        },
        
        onMarkerClick(problem) {
            // optional: tambahkan analytics atau tracking
            console.log('Marker clicked:', problem.title);
        },
        
        filterMarkers() {
            this.markerCluster.clearLayers();
            
            const filteredMarkers = this.markers.filter(({ problem }) => {
                // filter by difficulty
                if (this.filters.difficulty && problem.difficulty_level !== this.filters.difficulty) {
                    return false;
                }
                
                // filter urgent only
                if (this.filters.urgentOnly && !problem.is_urgent) {
                    return false;
                }
                
                // filter featured only
                if (this.filters.featuredOnly && !problem.is_featured) {
                    return false;
                }
                
                return true;
            });
            
            filteredMarkers.forEach(({ marker }) => {
                this.markerCluster.addLayer(marker);
            });
            
            // fit bounds ke filtered markers
            if (filteredMarkers.length > 0) {
                const group = new L.featureGroup(filteredMarkers.map(m => m.marker));
                this.map.fitBounds(group.getBounds().pad(0.1));
            }
        },
        
        resetMapFilters() {
            this.filters = {
                difficulty: '',
                urgentOnly: false,
                featuredOnly: false
            };
            this.filterMarkers();
        },
        
        calculateDaysLeft(deadline) {
            const now = new Date();
            const deadlineDate = new Date(deadline);
            const diffTime = deadlineDate - now;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            return Math.max(0, diffDays);
        },

        getProvinceCoordinates(problem) {
            // mapping koordinat untuk setiap province di Indonesia
            const provinceCoords = {
                'Aceh': { lat: 4.695135, lng: 96.749397 },
                'Sumatera Utara': { lat: 2.115320, lng: 99.545700 },
                'Sumatera Barat': { lat: -0.947420, lng: 100.418580 },
                'Riau': { lat: 0.293640, lng: 101.704540 },
                'Kepulauan Riau': { lat: 1.114700, lng: 104.045140 },
                'Jambi': { lat: -1.610640, lng: 103.613030 },
                'Sumatera Selatan': { lat: -3.119490, lng: 104.760580 },
                'Kepulauan Bangka Belitung': { lat: -2.741980, lng: 106.443970 },
                'Bengkulu': { lat: -3.792020, lng: 102.260940 },
                'Lampung': { lat: -4.558570, lng: 105.407540 },
                'DKI Jakarta': { lat: -6.208760, lng: 106.845600 },
                'Jawa Barat': { lat: -6.914740, lng: 107.609480 },
                'Banten': { lat: -6.402480, lng: 106.063300 },
                'Jawa Tengah': { lat: -7.150975, lng: 110.140260 },
                'DI Yogyakarta': { lat: -7.797068, lng: 110.370529 },
                'Jawa Timur': { lat: -7.539890, lng: 112.734320 },
                'Bali': { lat: -8.455840, lng: 115.188920 },
                'Nusa Tenggara Barat': { lat: -8.653150, lng: 117.361700 },
                'Nusa Tenggara Timur': { lat: -8.657240, lng: 121.079440 },
                'Kalimantan Barat': { lat: -0.026820, lng: 109.342500 },
                'Kalimantan Tengah': { lat: -1.681780, lng: 113.382350 },
                'Kalimantan Selatan': { lat: -3.097440, lng: 115.283580 },
                'Kalimantan Timur': { lat: 0.539280, lng: 116.419389 },
                'Kalimantan Utara': { lat: 3.073080, lng: 116.041580 },
                'Sulawesi Utara': { lat: 0.629800, lng: 123.975100 },
                'Gorontalo': { lat: 0.543960, lng: 123.058060 },
                'Sulawesi Tengah': { lat: -1.430380, lng: 121.445620 },
                'Sulawesi Barat': { lat: -2.844480, lng: 119.233170 },
                'Sulawesi Selatan': { lat: -3.664520, lng: 119.974620 },
                'Sulawesi Tenggara': { lat: -4.145040, lng: 122.174610 },
                'Maluku': { lat: -3.238740, lng: 130.145490 },
                'Maluku Utara': { lat: 1.570340, lng: 127.808380 },
                'Papua': { lat: -4.269930, lng: 138.080350 },
                'Papua Barat': { lat: -1.334150, lng: 133.174470 },
                'Papua Tengah': { lat: -3.978350, lng: 136.264950 },
                'Papua Pegunungan': { lat: -3.976870, lng: 138.683830 },
                'Papua Selatan': { lat: -6.086530, lng: 140.512480 },
                'Papua Barat Daya': { lat: -2.529120, lng: 132.291150 }
            };

            // cari koordinat berdasarkan nama province
            const provinceName = problem.province?.name || problem.location_province;
            if (provinceName && provinceCoords[provinceName]) {
                // tambahkan sedikit random offset agar marker tidak overlap jika di province yang sama
                const offset = 0.1;
                return {
                    lat: provinceCoords[provinceName].lat + (Math.random() - 0.5) * offset,
                    lng: provinceCoords[provinceName].lng + (Math.random() - 0.5) * offset
                };
            }

            // fallback ke koordinat tengah Indonesia
            return { lat: -2.5, lng: 118 };
        }
    };
}
</script>

<style>
/* custom marker styles - minimalist */
.custom-marker {
    background: transparent !important;
    border: none !important;
}

.custom-marker svg {
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
    cursor: pointer;
    transition: transform 0.1s ease;
}

.custom-marker:hover svg {
    transform: scale(1.1);
}

/* custom cluster icon styles - clean and simple */
.custom-cluster-icon {
    background: transparent !important;
    border: none !important;
}

.custom-cluster-icon .cluster-icon {
    display: flex;
    align-items: center;
    justify-content: center;
}

.custom-cluster-icon .cluster-icon svg {
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
    cursor: pointer;
    transition: transform 0.15s ease;
}

.custom-cluster-icon:hover .cluster-icon svg {
    transform: scale(1.05);
}

/* override default leaflet cluster styles */
.marker-cluster-small,
.marker-cluster-medium,
.marker-cluster-large {
    background: transparent !important;
}

.marker-cluster-small div,
.marker-cluster-medium div,
.marker-cluster-large div {
    background: transparent !important;
}

/* custom popup styles */
.custom-popup .leaflet-popup-content-wrapper {
    border-radius: 0.75rem;
    padding: 0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.custom-popup .leaflet-popup-content {
    margin: 0;
    width: 100% !important;
    min-width: 350px;
}

.custom-popup .leaflet-popup-tip {
    background: white;
}

/* ensure grid layout works in popup */
.custom-popup .grid {
    display: grid;
}

.custom-popup .grid-cols-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.custom-popup .col-span-2 {
    grid-column: span 2 / span 2;
}
</style>
@endpush