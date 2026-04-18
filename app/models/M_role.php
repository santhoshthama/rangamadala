<?php

require_once __DIR__ . '/M_director_role_core.php';
require_once __DIR__ . '/M_director_role_requests.php';
require_once __DIR__ . '/M_director_role_applications.php';
require_once __DIR__ . '/M_director_role_assignments.php';

class M_role {
    protected $roleCoreModel;
    protected $roleRequestsModel;
    protected $roleApplicationsModel;
    protected $roleAssignmentsModel;

    public function __construct() {
        $this->roleCoreModel = new M_director_role_core();
        $this->roleRequestsModel = new M_director_role_requests();
        $this->roleApplicationsModel = new M_director_role_applications();
        $this->roleAssignmentsModel = new M_director_role_assignments();
    }

    // CREATE - Add a new role
    public function createRole($data) {
        return $this->roleCoreModel->createRole($data);
    }

    // READ - Get all roles for a drama
    public function getRolesByDrama($drama_id) {
        return $this->roleCoreModel->getRolesByDrama($drama_id);
    }

    // READ - Get single role by ID
    public function getRoleById($role_id) {
        return $this->roleCoreModel->getRoleById($role_id);
    }

    // UPDATE - Update role
    public function updateRole($role_id, $data) {
        return $this->roleCoreModel->updateRole($role_id, $data);
    }

    // DELETE - Delete role
    public function deleteRole($role_id) {
        return $this->roleCoreModel->deleteRole($role_id);
    }

    // Get role statistics for a drama
    public function getRoleStats($drama_id) {
        return $this->roleCoreModel->getRoleStats($drama_id);
    }

    // Get applications for a role
    public function getApplicationsByRole($role_id) {
        return $this->roleApplicationsModel->getApplicationsByRole($role_id);
    }

    public function getApplicationById($application_id) {
        return $this->roleApplicationsModel->getApplicationById($application_id);
    }

    public function markApplicationProfileViewed(int $application_id, int $director_id) {
        return $this->roleApplicationsModel->markApplicationProfileViewed($application_id, $director_id);
    }

    public function scheduleApplicationInterview(int $application_id, string $interviewAt, int $director_id, ?string $notes = null) {
        return $this->roleApplicationsModel->scheduleApplicationInterview($application_id, $interviewAt, $director_id, $notes);
    }

    // Get all pending applications for a drama
    public function getPendingApplications($drama_id) {
        return $this->roleApplicationsModel->getPendingApplications($drama_id);
    }

    // Get assigned artists for a role
    public function getAssignmentsByRole($role_id) {
        return $this->roleAssignmentsModel->getAssignmentsByRole($role_id);
    }

    public function getAssignmentById($assignment_id) {
        return $this->roleAssignmentsModel->getAssignmentById($assignment_id);
    }

    // Accept application and assign role
    public function acceptApplication($application_id, $reviewed_by) {
        return $this->roleApplicationsModel->acceptApplication($application_id, $reviewed_by);
    }

    // Reject application
    public function rejectApplication($application_id, $reviewed_by) {
        return $this->roleApplicationsModel->rejectApplication($application_id, $reviewed_by);
    }

    public function getApplicationForArtist(int $application_id, int $artist_id) {
        return $this->roleApplicationsModel->getApplicationForArtist($application_id, $artist_id);
    }

    public function artistRespondInterview(int $application_id, int $artist_id, string $response, ?string $note = null) {
        return $this->roleApplicationsModel->artistRespondInterview($application_id, $artist_id, $response, $note);
    }

    public function markInterviewConfirmationSeen(int $application_id, int $director_id) {
        return $this->roleApplicationsModel->markInterviewConfirmationSeen($application_id, $director_id);
    }

    public function getApplicationsByDrama($drama_id, ?string $status = null) {
        return $this->roleApplicationsModel->getApplicationsByDrama($drama_id, $status);
    }

    public function publishVacancy($role_id, ?string $message, int $director_id) {
        return $this->roleCoreModel->publishVacancy($role_id, $message, $director_id);
    }

    public function unpublishVacancy($role_id) {
        return $this->roleCoreModel->unpublishVacancy($role_id);
    }

    public function getPublishedRolesByDrama($drama_id) {
        return $this->roleCoreModel->getPublishedRolesByDrama($drama_id);
    }

    public function createRoleRequest($role_id, $artist_id, $director_id, ?string $note = null, ?string $interviewAt = null) {
        return $this->roleRequestsModel->createRoleRequest($role_id, $artist_id, $director_id, $note, $interviewAt);
    }

    public function getRoleRequestsByDrama($drama_id, ?string $status = null) {
        return $this->roleRequestsModel->getRoleRequestsByDrama($drama_id, $status);
    }

    public function getRoleRequestsByRole($role_id, ?string $status = null) {
        return $this->roleRequestsModel->getRoleRequestsByRole($role_id, $status);
    }

    public function getRoleRequestById($request_id) {
        return $this->roleRequestsModel->getRoleRequestById($request_id);
    }

    public function updateRoleRequestStatus($request_id, string $status, ?string $note = null, ?string $interviewAt = null) {
        return $this->roleRequestsModel->updateRoleRequestStatus($request_id, $status, $note, $interviewAt);
    }

    public function cancelRoleRequestByDirector(int $request_id, int $director_id, int $drama_id): bool {
        return $this->roleRequestsModel->cancelRoleRequestByDirector($request_id, $director_id, $drama_id);
    }

    public function assignArtistFromRequest($request_id, int $director_id) {
        return $this->roleRequestsModel->assignArtistFromRequest($request_id, $director_id);
    }

    /**
     * Count all published vacancies across all dramas
     */
    public function countPublishedVacancies() {
        return $this->roleCoreModel->countPublishedVacancies();
    }

    /**
     * Get all published vacancies with optional filters
     */
    public function getAllPublishedVacancies($filters = []) {
        return $this->roleCoreModel->getAllPublishedVacancies($filters);
    }

    /**
     * Apply for a role (artist submits application)
     */
    public function applyForRole($role_id, $artist_id, $cover_letter = '', $media_links = '') {
        return $this->roleApplicationsModel->applyForRole($role_id, $artist_id, $cover_letter, $media_links);
    }

    /**
     * Get role details for application form
     */
    public function getRoleDetailsForApplication($role_id) {
        return $this->roleApplicationsModel->getRoleDetailsForApplication($role_id);
    }

    /**
     * Get all applications submitted by an artist
     */
    public function getArtistApplications($artist_id) {
        return $this->roleApplicationsModel->getArtistApplications($artist_id);
    }

    /**
     * Get all role assignments for an artist (roles they are currently cast in)
     */
    public function getAssignmentsByArtist($artist_id) {
        return $this->roleAssignmentsModel->getAssignmentsByArtist($artist_id);
    }

    /**
     * Get artist's specific role in a drama
     */
    public function getArtistRoleInDrama($artist_id, $drama_id) {
        return $this->roleAssignmentsModel->getArtistRoleInDrama($artist_id, $drama_id);
    }

    /**
     * Remove artist assignment from role
     * @param int $assignment_id Assignment ID to remove
     * @return bool Success status
     */
    public function removeAssignment($assignment_id) {
        return $this->roleAssignmentsModel->removeAssignment($assignment_id);
    }
}

?>