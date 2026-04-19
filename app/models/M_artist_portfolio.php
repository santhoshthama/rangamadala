<?php

class M_artist_portfolio extends M_signup
{
    public function getByArtistId($artistId)
    {
        try {
            $this->db->query('SELECT * FROM artist_portfolios WHERE artist_id = :artist_id ORDER BY updated_at DESC, id DESC');
            $this->db->bind(':artist_id', $artistId);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log('Error in M_artist_portfolio::getByArtistId: ' . $e->getMessage());
            return [];
        }
    }

    public function getByIdAndArtistId($id, $artistId)
    {
        try {
            $this->db->query('SELECT * FROM artist_portfolios WHERE id = :id AND artist_id = :artist_id LIMIT 1');
            $this->db->bind(':id', $id);
            $this->db->bind(':artist_id', $artistId);
            return $this->db->single();
        } catch (Exception $e) {
            error_log('Error in M_artist_portfolio::getByIdAndArtistId: ' . $e->getMessage());
            return null;
        }
    }

    public function create($artistId, array $data)
    {
        try {
            $this->db->query('INSERT INTO artist_portfolios (artist_id, past_dramas, position_worked, years_in_industry, specialized_fields, education_qualifications, created_at, updated_at)
                              VALUES (:artist_id, :past_dramas, :position_worked, :years_in_industry, :specialized_fields, :education_qualifications, NOW(), NOW())');

            $this->db->bind(':artist_id', $artistId);
            $this->db->bind(':past_dramas', $data['past_dramas']);
            $this->db->bind(':position_worked', $data['position_worked']);
            $this->db->bind(':years_in_industry', $data['years_in_industry']);
            $this->db->bind(':specialized_fields', $data['specialized_fields']);
            $this->db->bind(':education_qualifications', $data['education_qualifications']);

            return $this->db->execute();
        } catch (Exception $e) {
            error_log('Error in M_artist_portfolio::create: ' . $e->getMessage());
            return false;
        }
    }

    public function updateByIdAndArtistId($id, $artistId, array $data)
    {
        try {
            $this->db->query('UPDATE artist_portfolios
                              SET past_dramas = :past_dramas,
                                  position_worked = :position_worked,
                                  years_in_industry = :years_in_industry,
                                  specialized_fields = :specialized_fields,
                                  education_qualifications = :education_qualifications,
                                  updated_at = NOW()
                              WHERE id = :id AND artist_id = :artist_id');

            $this->db->bind(':id', $id);
            $this->db->bind(':artist_id', $artistId);
            $this->db->bind(':past_dramas', $data['past_dramas']);
            $this->db->bind(':position_worked', $data['position_worked']);
            $this->db->bind(':years_in_industry', $data['years_in_industry']);
            $this->db->bind(':specialized_fields', $data['specialized_fields']);
            $this->db->bind(':education_qualifications', $data['education_qualifications']);

            return $this->db->execute();
        } catch (Exception $e) {
            error_log('Error in M_artist_portfolio::updateByIdAndArtistId: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteByIdAndArtistId($id, $artistId)
    {
        try {
            $this->db->query('DELETE FROM artist_portfolios WHERE id = :id AND artist_id = :artist_id');
            $this->db->bind(':id', $id);
            $this->db->bind(':artist_id', $artistId);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log('Error in M_artist_portfolio::deleteByIdAndArtistId: ' . $e->getMessage());
            return false;
        }
    }
}
