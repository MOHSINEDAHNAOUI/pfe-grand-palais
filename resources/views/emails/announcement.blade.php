@extends('emails.layout')

@section('title', $title)

@section('content')
<div style="padding: 40px 30px;">
    <div style="text-align: center; margin-bottom: 40px;">
        <h1 style="font-family: 'Georgia', serif; font-size: 32px; font-weight: 300; margin-bottom: 12px; color: #0f172a;">{{ $title }}</h1>
        <div style="width: 40px; h-px; background: #C9A96E; margin: 20px auto;"></div>
    </div>

    <div style="background: #ffffff; border: 1px solid #f1f5f9; border-radius: 20px; padding: 40px; margin-bottom: 40px; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
        <p style="font-size: 16px; color: #334155; line-height: 1.8; margin-bottom: 30px;">
            {!! nl2br(e($messageContent)) !!}
        </p>

        @if($actionLabel && $actionUrl)
        <div style="text-align: center; margin-top: 40px;">
            <a href="{{ $actionUrl }}" style="display: inline-block; background: #0f172a; color: #ffffff; text-decoration: none; padding: 18px 40px; border-radius: 12px; font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.1em; transition: all 0.3s;">
                {{ $actionLabel }}
            </a>
        </div>
        @endif
    </div>

    <div style="text-align: center; margin-top: 40px;">
        <p style="font-size: 12px; color: #94a3b8; text-transform: uppercase; letter-spacing: 2px; font-weight: bold; margin-bottom: 10px;">
            L'Excellence au Sommet
        </p>
        <p style="font-family: 'Georgia', serif; font-size: 16px; font-style: italic; color: #64748b;">
            Grand Palais Luxury Hotel
        </p>
    </div>
</div>
@endsection
