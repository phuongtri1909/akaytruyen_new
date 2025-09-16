@extends('Admin.layouts.sidebar')

@section('title', 'Dashboard')

@section('main-content')
    <div class="category-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item current">Dashboard</li>
            </ol>
        </div>

        <!-- Welcome Section -->
        <div class="content-card">
            <div class="card-content">
                <div class="welcome-section">
                    <div class="welcome-content">
                        <h2>Chào mừng {{ Auth::user()->name }}! 🎉</h2>
                        <h4>Đây là hệ thống quản lý truyện!</h4>
                        <a href="{{ route('home') }}" class="home-button">
                            <button>Về trang chính để đọc truyện nhé</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-chart-line icon-title"></i>
                    <h5>Thống kê tổng quan</h5>
                </div>
            </div>
            <div class="card-content">
                <div class="stats-grid">
                    <!-- Total Stories -->
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ number_format($totalStory) }}</h3>
                            <p>Tổng số truyện</p>
                        </div>
                    </div>

                    <!-- Total Chapters -->
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-list"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ number_format($totalChapter) }}</h3>
                            <p>Tổng số chương</p>
                        </div>
                    </div>

                    <!-- Total Views -->
                    <div class="stat-card highlight">
                        <div class="stat-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ number_format($totalViews) }}</h3>
                            <p>Tổng lượt xem</p>
                        </div>
                    </div>

                    <!-- Total Ratings -->
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ number_format($totalRating) }}</h3>
                            <p>Tổng lượt đánh giá</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Story Rankings -->
        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-trophy icon-title"></i>
                    <h5>Xếp hạng truyện</h5>
                </div>
            </div>
            <div class="card-content">
                <div class="rankings-grid">
                    <!-- Daily Ranking -->
                    <div class="ranking-card">
                        <div class="ranking-header">
                            <h6><i class="fas fa-calendar-day"></i> Xếp hạng ngày</h6>
                            <a href="{{ route('admin.ratings.index') }}" class="ranking-link">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                        <div class="ranking-content">
                            @if($ratingsDay && $storiesDay->count() > 0)
                                <ul class="story-list">
                                    @foreach($storiesDay->take(5) as $k => $story)
                                        <li class="story-item">
                                            <div class="story-image">
                                                <img src="{{ \App\Helpers\Helper::getStoryImageUrl($story->image) }}" alt="{{ $story->name }}" class="rounded">
                                            </div>
                                            <div class="story-info">
                                                <h6 class="story-title">{{ $story->name }}</h6>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="empty-ranking">
                                    <p>Chưa có dữ liệu xếp hạng ngày</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Monthly Ranking -->
                    <div class="ranking-card">
                        <div class="ranking-header">
                            <h6><i class="fas fa-calendar-alt"></i> Xếp hạng tháng</h6>
                            <a href="{{ route('admin.ratings.index') }}" class="ranking-link">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                        <div class="ranking-content">
                            @if($ratingsMonth && $storiesMonth->count() > 0)
                                <ul class="story-list">
                                    @foreach($storiesMonth->take(5) as $k => $story)
                                        <li class="story-item">
                                            <div class="story-image">
                                                <img src="{{ \App\Helpers\Helper::getStoryImageUrl($story->image) }}" alt="{{ $story->name }}" class="rounded">
                                            </div>
                                            <div class="story-info">
                                                <h6 class="story-title">{{ $story->name }}</h6>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="empty-ranking">
                                    <p>Chưa có dữ liệu xếp hạng tháng</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- All Time Ranking -->
                    <div class="ranking-card">
                        <div class="ranking-header">
                            <h6><i class="fas fa-crown"></i> Xếp hạng all time</h6>
                            <a href="{{ route('admin.ratings.index') }}" class="ranking-link">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                        <div class="ranking-content">
                            @if($ratingsAllTime && $storiesAllTime->count() > 0)
                                <ul class="story-list">
                                    @foreach($storiesAllTime->take(5) as $k => $story)
                                        <li class="story-item">
                                            <div class="story-image">
                                                <img src="{{ \App\Helpers\Helper::getStoryImageUrl($story->image) }}" alt="{{ $story->name }}" class="rounded">
                                            </div>
                                            <div class="story-info">
                                                <h6 class="story-title">{{ $story->name }}</h6>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="empty-ranking">
                                    <p>Chưa có dữ liệu xếp hạng all time</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Welcome Section */
        .welcome-section {
            text-align: center;
            padding: 40px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            color: white;
        }

        .welcome-content h2 {
            margin: 0 0 10px 0;
            font-size: 28px;
            font-weight: bold;
        }

        .welcome-content h4 {
            margin: 0 0 25px 0;
            font-size: 18px;
            opacity: 0.9;
        }

        .home-button {
            text-decoration: none;
        }

        .home-button button {
            background-color: #4CAF50;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .home-button button:hover {
            background-color: #45a049;
        }

        .home-button button:active {
            background-color: #3e8e41;
            transform: scale(0.98);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        /* Rankings Grid */
        .rankings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .ranking-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            border: 1px solid #e9ecef;
        }

        .ranking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .ranking-header h6 {
            margin: 0;
            color: #333;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ranking-link {
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
        }

        .ranking-link:hover {
            color: #0056b3;
        }

        .story-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .story-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .story-item:last-child {
            border-bottom: none;
        }

        .story-image {
            width: 46px;
            height: 46px;
            flex-shrink: 0;
        }

        .story-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .story-info {
            flex: 1;
        }

        .story-title {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            line-height: 1.3;
        }

        .empty-ranking {
            text-align: center;
            padding: 20px;
            color: #6c757d;
            font-style: italic;
        }

        .stat-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .stat-card.highlight {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            background: #007bff;
            color: white;
        }

        .stat-card.highlight .stat-icon {
            background: rgba(255,255,255,0.2);
        }

        .stat-content h3 {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            color: #333;
        }

        .stat-card.highlight .stat-content h3 {
            color: white;
        }

        .stat-content p {
            margin: 5px 0 0 0;
            color: #6c757d;
            font-size: 13px;
        }

        .stat-card.highlight .stat-content p {
            color: rgba(255,255,255,0.8);
        }


        /* Responsive Design */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .rankings-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .stat-card {
                padding: 15px;
            }
            
            .ranking-card {
                padding: 15px;
            }
            
            .stat-content h3 {
                font-size: 20px;
            }
            
            .welcome-content h2 {
                font-size: 24px;
            }
            
            .welcome-content h4 {
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            .stat-content h3 {
                font-size: 18px;
            }
            
            .stat-content p {
                font-size: 12px;
            }
            
            .ranking-header h6 {
                font-size: 14px;
            }
            
            .story-title {
                font-size: 13px;
            }
            
            .welcome-content h2 {
                font-size: 20px;
            }
            
            .welcome-content h4 {
                font-size: 14px;
            }
            
            .home-button button {
                padding: 12px 24px;
                font-size: 14px;
            }
        }
    </style>
@endpush

@push('scripts')
   
@endpush
