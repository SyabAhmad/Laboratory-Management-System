@extends('Layout.master')

@section('title', 'Page Not Found')

@section('content')
<style>
    .error-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 70vh;
        padding: 20px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    .error-card {
        text-align: center;
        max-width: 600px;
        width: 100%;
        padding: 40px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.08);
        transition: all 0.4s ease;
        position: relative;
        overflow: hidden;
    }
    
    .error-card:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #8d2d36, #a84a54);
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
    }
    
    .error-card:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 20px 50px rgba(0,0,0,0.12);
    }
    
    .error-icon {
        font-size: 5rem;
        color: #e74c3c;
        margin-bottom: 20px;
        animation: bounce 2s infinite;
    }
    
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-15px); }
    }
    
    .error-title {
        font-size: 2.8rem;
        color: #2c3e50;
        margin-bottom: 15px;
        font-weight: 800;
        letter-spacing: -0.5px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.05);
    }
    
    .error-message {
        font-size: 1.2rem;
        color: #7f8c8d;
        margin-bottom: 30px;
        line-height: 1.6;
        font-weight: 500;
    }
    
    .btn-home {
        background-color: #8d2d36;
        border-color: #8d2d36;
        padding: 12px 30px;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 10px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 15px rgba(141, 45, 54, 0.2);
        text-decoration: none;
        color: white;
    }
    
    .btn-home:hover {
        background-color: #a84a54;
        border-color: #a84a54;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(141, 45, 54, 0.3);
    }
    
    .error-actions {
        margin-top: 30px;
    }
    
    .action-link {
        display: inline-block;
        margin: 0 10px;
        color: #8d2d36;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
        padding: 8px 12px;
        border-radius: 6px;
        position: relative;
    }
    
    .action-link:hover {
        text-decoration: none;
        color: #a84a54;
        background-color: rgba(168, 74, 84, 0.1);
        transform: translateY(-1px);
    }
    
    .action-link::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 10%;
        right: 10%;
        height: 2px;
        background: #8d2d36;
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }
    
    .action-link:hover::after {
        transform: scaleX(1);
    }
    
    .error-footer {
        margin-top: 30px;
        font-size: 0.9rem;
        color: #95a5a6;
        font-weight: 400;
        opacity: 0.8;
    }
</style>

<div class="error-container">
    <div class="error-card">
        <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        
        <h1 class="error-title">Oops! Page Not Found</h1>
        
        <p class="error-message">
            The page you're looking for doesn't exist or has been moved. 
            Don't worry, we'll help you find your way back.
        </p>
        
        <div class="error-actions">
            <a href="{{ route('dashboard') }}" class="btn btn-home">
                <i class="fas fa-home mr-2"></i>Return to Dashboard
            </a>
            
            <div class="mt-4">
                <a href="{{ url()->previous() }}" class="action-link">
                    <i class="fas fa-arrow-left mr-1"></i>Go Back
                </a>
                
                <a href="javascript:history.back()" class="action-link">
                    <i class="fas fa-redo mr-1"></i>Refresh Page
                </a>
            </div>
        </div>
        
        <div class="error-footer">
            If you continue to have issues, please contact support.
        </div>
    </div>
</div>
@endsection

