<?php

class Artistportfolio
{
    use Controller;

    public function index()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
            header('Location: ' . ROOT . '/login');
            exit;
        }

        if ($_SESSION['user_role'] !== 'artist') {
            header('Location: ' . ROOT . '/profile');
            exit;
        }

        $artistId = (int)$_SESSION['user_id'];
        $portfolioModel = new M_artist_portfolio();

        $data = [
            'errors' => [],
            'success' => '',
            'entries' => [],
            'edit_item' => null,
            'form' => [
                'past_dramas' => '',
                'position_worked' => '',
                'years_in_industry' => '',
                'specialized_fields' => '',
                'education_qualifications' => ''
            ]
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? 'create';

            if ($action === 'delete') {
                $deleteId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
                if ($deleteId > 0 && $portfolioModel->deleteByIdAndArtistId($deleteId, $artistId)) {
                    $data['success'] = 'Portfolio entry deleted successfully.';
                } else {
                    $data['errors'][] = 'Unable to delete portfolio entry.';
                }
            } else {
                $entryId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

                $form = [
                    'past_dramas' => trim($_POST['past_dramas'] ?? ''),
                    'position_worked' => trim($_POST['position_worked'] ?? ''),
                    'years_in_industry' => trim($_POST['years_in_industry'] ?? ''),
                    'specialized_fields' => trim($_POST['specialized_fields'] ?? ''),
                    'education_qualifications' => trim($_POST['education_qualifications'] ?? '')
                ];

                $data['form'] = $form;
                $errors = $this->validateForm($form);

                if (empty($errors)) {
                    $payload = [
                        'past_dramas' => $form['past_dramas'],
                        'position_worked' => $form['position_worked'],
                        'years_in_industry' => (int)$form['years_in_industry'],
                        'specialized_fields' => $form['specialized_fields'],
                        'education_qualifications' => $form['education_qualifications']
                    ];

                    if ($action === 'update' && $entryId > 0) {
                        if ($portfolioModel->updateByIdAndArtistId($entryId, $artistId, $payload)) {
                            $data['success'] = 'Portfolio entry updated successfully.';
                            $data['form'] = [
                                'past_dramas' => '',
                                'position_worked' => '',
                                'years_in_industry' => '',
                                'specialized_fields' => '',
                                'education_qualifications' => ''
                            ];
                        } else {
                            $data['errors'][] = 'Unable to update portfolio entry.';
                        }
                    } else {
                        if ($portfolioModel->create($artistId, $payload)) {
                            $data['success'] = 'Portfolio entry added successfully.';
                            $data['form'] = [
                                'past_dramas' => '',
                                'position_worked' => '',
                                'years_in_industry' => '',
                                'specialized_fields' => '',
                                'education_qualifications' => ''
                            ];
                        } else {
                            $data['errors'][] = 'Unable to add portfolio entry.';
                        }
                    }
                } else {
                    $data['errors'] = $errors;
                }
            }
        }

        $editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
        if ($editId > 0) {
            $editItem = $portfolioModel->getByIdAndArtistId($editId, $artistId);
            if ($editItem) {
                $data['edit_item'] = $editItem;
                if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                    $data['form'] = [
                        'past_dramas' => $editItem->past_dramas ?? '',
                        'position_worked' => $editItem->position_worked ?? '',
                        'years_in_industry' => isset($editItem->years_in_industry) ? (string)$editItem->years_in_industry : '',
                        'specialized_fields' => $editItem->specialized_fields ?? '',
                        'education_qualifications' => $editItem->education_qualifications ?? ''
                    ];
                }
            }
        }

        $data['entries'] = $portfolioModel->getByArtistId($artistId);
        $this->view('artist_portfolio', $data);
    }

    private function validateForm(array $form)
    {
        $errors = [];

        if ($form['past_dramas'] === '') {
            $errors[] = 'Past dramas field is required.';
        }

        if ($form['position_worked'] === '') {
            $errors[] = 'Position worked field is required.';
        }

        if ($form['years_in_industry'] === '') {
            $errors[] = 'Years in the industry is required.';
        } elseif (!ctype_digit($form['years_in_industry'])) {
            $errors[] = 'Years in the industry must be a whole number.';
        }

        if ($form['specialized_fields'] === '') {
            $errors[] = 'Specialized fields field is required.';
        }

        if ($form['education_qualifications'] === '') {
            $errors[] = 'Education qualifications field is required.';
        }

        return $errors;
    }
}
