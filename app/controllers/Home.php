<?php

class Home
{
    use Controller;

    public function index()
    {
        $db = new Database();
        
        // Fetch active swiper slides
        $db->query("SELECT * FROM swiper_slides WHERE is_active = 1 ORDER BY display_order ASC");
        $swiperSlides = $db->resultSet();
        
        // Fetch active gallery images
        $db->query("SELECT * FROM gallery_images WHERE is_active = 1 ORDER BY display_order ASC");
        $galleryImages = $db->resultSet();
        
        // Fetch active testimonials
        $db->query("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY display_order ASC");
        $testimonials = $db->resultSet();
        
        $data = [
            'swiperSlides' => $swiperSlides,
            'galleryImages' => $galleryImages,
            'testimonials' => $testimonials
        ];
        
        $this->view('home', $data);
    }

    /**
     * Learn more page for the project idea
     */
    public function learnMore()
    {
        $this->view('learn_more');
    }

    /**
     * Public drama details page - no login required
     * Anyone can view drama information
     */
    public function drama($drama_id = null)
    {
        if (!$drama_id) {
            header("Location: " . ROOT . "/Home");
            exit;
        }

        $db = new Database();
        
        // Fetch drama details with category
        $db->query("SELECT d.*, c.name as category_name, u.full_name as creator_name, u.phone as producer_phone, u.email as producer_email
                    FROM dramas d 
                    LEFT JOIN categories c ON d.category_id = c.id 
                    LEFT JOIN users u ON d.created_by = u.id
                    WHERE d.id = :id AND d.is_published = 1");
        $db->bind(':id', $drama_id);
        $drama = $db->single();

        if (!$drama) {
            header("Location: " . ROOT . "/Home");
            exit;
        }

        // Get rating summary (public info)
        $db->query("SELECT 
                        COUNT(id) as total_ratings,
                        ROUND(AVG(rating), 2) as average_rating
                    FROM drama_ratings 
                    WHERE drama_id = :drama_id");
        $db->bind(':drama_id', $drama_id);
        $rating_summary = $db->single();

        $data = [
            'drama' => $drama,
            'rating_summary' => $rating_summary
        ];

        $this->view('public_drama_details', $data);
    }
}
