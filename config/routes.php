<?php

/**
 * Định nghĩa tất cả routes và middleware
 */

// Define routes
$routes = [
    '/' => 'HomeController@index',
    '/login' => 'AuthController@login',
    '/register' => 'AuthController@register',
    '/logout' => 'AuthController@logout',
    '/forgot-password' => 'AuthController@forgotPassword',
    '/user/profile' => 'UserController@profile',
    '/user/edit-profile' => 'UserController@editProfile',
    '/user/change-password' => 'UserController@changePassword',
    '/user/update-profile' => 'UserController@updateProfile',
    '/user/update-password' => 'UserController@updatePassword',
    '/user/update-avatar' => 'UserController@updateAvatar',
    '/booking/(\d+)' => 'BookingController@create',
    '/booking/cart' => 'BookingController@cart',

    // Thêm routes cho trang tìm kiếm
    '/search' => 'SearchController@index',
    '/search/hotels' => 'SearchController@hotels',
    '/search/results' => 'SearchController@results',
    '/api/search/hotels' => 'ApiController@searchHotels',

    '/hotels/(\d+)/([^/]+)' => 'HotelController@show',
    '/hotels/(\d+)' => 'HotelController@show',
    '/hotels' => 'HotelController@index',
    '/hotels/getAvailableRooms' => 'HotelController@getAvailableRooms',

    '/regions/([^/]+)' => 'DestinationController@byRegion',

    // API routes
    '/api/destinations' => 'ApiController@getDestinations',
    '/api/destinations/search' => 'ApiController@searchDestinations',
    '/api/hotels/(\d+)/promotions' => 'ApiController@getHotelPromotions',
    '/api/hotels/(\d+)/reviews' => 'ReviewController@getHotelReviews',
    '/api/reviews' => 'ReviewController@addReview',
    '/api/reviews/(\d+)' => 'ReviewController@deleteReview',
    '/api/favorites/toggle' => 'ApiController@toggleFavorite',

    '/promotions' => 'PromotionController@index',
    '/promotions/(\d+)' => 'PromotionController@show',

    '/user/remove_favorite' => 'UserController@removeFavorite',
    '/user/cancel-booking' => 'UserController@cancelBooking',


    '/booking/checkout' => 'BookingController@checkout',
    '/booking/process' => 'BookingController@process',

    // Thêm routes cho nhân viên khách sạn (hotel_staff)
    '/hotel_staff/dashboard' => 'HotelStaffController@dashboard',
    '/hotel_staff/manage_hotel/(\d+)' => 'HotelStaffController@manage_hotel',
    '/hotel_staff/manage_hotel' => 'HotelStaffController@manage_hotel',
    '/hotel_staff/manage_rooms/(\d+)' => 'HotelStaffController@manage_rooms',
    '/hotel_staff/manage_rooms' => 'HotelStaffController@manage_rooms',
    '/hotel_staff/getAmenities' => 'HotelStaffController@getAmenities',
    '/hotel_staff/getAppliedAmenities/(\d+)' => 'HotelStaffController@getAppliedAmenities',
    '/hotel_staff/updateStatus' => 'HotelStaffController@updateStatus',
    '/hotel_staff/updateHotel' => 'HotelStaffController@updateHotel',
    '/hotel_staff/updateLocation' => 'HotelStaffController@updateLocation',
    '/hotel_staff/uploadHotelPhotos' => 'HotelStaffController@uploadHotelPhotos',
    '/hotel_staff/updateAmenities' => 'HotelStaffController@updateAmenities',
    '/hotel_staff/getPromotions' => 'HotelStaffController@getPromotions',
    '/hotel_staff/getAppliedPromotions/(\d+)' => 'HotelStaffController@getAppliedPromotions',
    '/hotel_staff/updatePromotions' => 'HotelStaffController@updatePromotions',
    '/hotel_staff/addRoom' => 'HotelStaffController@addRoom',
    '/hotel_staff/filterRooms' => 'HotelStaffController@filterRooms',
    '/hotel_staff/getRoomDetails/(\d+)' => 'HotelStaffController@getRoomDetails',
    '/hotel_staff/editRoom' => 'HotelStaffController@editRoom',
    '/hotel_staff/deleteRoom/(\d+)' => 'HotelStaffController@deleteRoom',
    '/hotel_staff/getRoomStats' => 'HotelStaffController@getRoomStats',
    '/hotel_staff/getDashboardData' => 'HotelStaffController@getDashboardData',
];

$middlewares = [
    // Các route yêu cầu đăng nhập
    '/user/profile' => 'AuthMiddleware',
    '/user/edit-profile' => 'AuthMiddleware',
    '/user/change-password' => 'AuthMiddleware',
    '/user/update-profile' => 'AuthMiddleware',
    '/user/update-password' => 'AuthMiddleware',
    '/user/update-avatar' => 'AuthMiddleware',
    '/user/remove_favorite' => 'AuthMiddleware',
    '/user/cancel-booking' => 'AuthMiddleware',
    '/booking/(\d+)' => 'AuthMiddleware',
    '/booking/cart' => 'AuthMiddleware',
    '/booking/checkout' => 'AuthMiddleware',
    '/booking/process' => 'AuthMiddleware',
    '/api/reviews' => 'AuthMiddleware',
    '/api/reviews/(\d+)' => 'AuthMiddleware',
    '/api/favorites/toggle' => 'AuthMiddleware',

    // Hotel Staff routes yêu cầu đăng nhập và là hotel_staff
    '/hotel_staff/dashboard' => ['AuthMiddleware', 'HotelStaffMiddleware'],
    '/hotel_staff/manage_hotel/(\d+)' => ['AuthMiddleware', 'HotelStaffMiddleware'],
    '/hotel_staff/manage_hotel' => ['AuthMiddleware', 'HotelStaffMiddleware'],
    '/hotel_staff/manage_rooms/(\d+)' => ['AuthMiddleware', 'HotelStaffMiddleware'],
    '/hotel_staff/manage_rooms' => ['AuthMiddleware', 'HotelStaffMiddleware'],
    '/hotel_staff/getAmenities' => ['AuthMiddleware', 'HotelStaffMiddleware'],
    '/hotel_staff/getAppliedAmenities/(\d+)' => ['AuthMiddleware', 'HotelStaffMiddleware'],
    '/hotel_staff/updateStatus' => ['AuthMiddleware', 'HotelStaffMiddleware'],
    '/hotel_staff/updateHotel' => ['AuthMiddleware', 'HotelStaffMiddleware'],
    '/hotel_staff/updateLocation' => ['AuthMiddleware', 'HotelStaffMiddleware'],
    '/hotel_staff/uploadHotelPhotos' => ['AuthMiddleware', 'HotelStaffMiddleware'],
    '/hotel_staff/updateAmenities' => ['AuthMiddleware', 'HotelStaffMiddleware'],
    '/hotel_staff/getPromotions' => ['AuthMiddleware', 'HotelStaffMiddleware'],
    '/hotel_staff/getAppliedPromotions/(\d+)' => ['AuthMiddleware', 'HotelStaffMiddleware'],
    '/hotel_staff/updatePromotions' => ['AuthMiddleware', 'HotelStaffMiddleware'],
    '/hotel_staff/addRoom' => ['AuthMiddleware', 'HotelStaffMiddleware'],
    '/hotel_staff/filterRooms' => ['AuthMiddleware', 'HotelStaffMiddleware'],
    '/hotel_staff/getRoomDetails/(\d+)' => ['AuthMiddleware', 'HotelStaffMiddleware'],
    '/hotel_staff/editRoom' => ['AuthMiddleware', 'HotelStaffMiddleware'],
    '/hotel_staff/deleteRoom/(\d+)' => ['AuthMiddleware', 'HotelStaffMiddleware'],
    '/hotel_staff/getRoomStats' => ['AuthMiddleware', 'HotelStaffMiddleware'],
    '/hotel_staff/getDashboardData' => ['AuthMiddleware', 'HotelStaffMiddleware'],
];
