package main

import (
	"encoding/json"
	"fmt"
	"os"
	"path/filepath"
	"testing"
	"time"

	"github.com/stretchr/testify/assert"
)

type BlogPost struct {
	ID       int    `json:"id"`
	Filename string `json:"filename"`
	Title    string `json:"title"`
	Slug     string `json:"slug"`
	Date     string `json:"date"`
	Author   string `json:"author"`
	Content  string `json:"content"`
}

func (suite *RegressionTestSuite) TestBlogPostCRUD() {
	// Test data
	testPost := BlogPost{
		Title:   "Regression Test Post",
		Slug:    "regression-test-post",
		Date:    time.Now().Format("2006-01-02"),
		Author:  "Test Author",
		Content: "This is a test post for regression testing.",
	}

	// Test 1: Create BlogPost
	suite.T().Run("Create BlogPost", func(t *testing.T) {
		resp, err := suite.makeRequest("POST", "/api/blog-posts", testPost)
		assert.NoError(t, err)
		defer resp.Body.Close()

		assert.Equal(t, 201, resp.StatusCode, "Should create blog post successfully")

		var createdPost BlogPost
		err = json.NewDecoder(resp.Body).Decode(&createdPost)
		assert.NoError(t, err)
		assert.NotZero(t, createdPost.ID)
		assert.Equal(t, testPost.Title, createdPost.Title)
		assert.Equal(t, testPost.Slug+".md", createdPost.Filename)

		testPost.ID = createdPost.ID
		testPost.Filename = createdPost.Filename

		// Verify file creation
		suite.verifyFileExists(createdPost.Filename)
	})

	// Test 2: Read BlogPost
	suite.T().Run("Read BlogPost", func(t *testing.T) {
		resp, err := suite.makeRequest("GET", fmt.Sprintf("/api/blog-posts/%d", testPost.ID), nil)
		assert.NoError(t, err)
		defer resp.Body.Close()

		assert.Equal(t, 200, resp.StatusCode, "Should read blog post successfully")

		var retrievedPost BlogPost
		err = json.NewDecoder(resp.Body).Decode(&retrievedPost)
		assert.NoError(t, err)
		assert.Equal(t, testPost.ID, retrievedPost.ID)
		assert.Equal(t, testPost.Title, retrievedPost.Title)
	})

	// Test 3: Update BlogPost
	suite.T().Run("Update BlogPost", func(t *testing.T) {
		updatedPost := testPost
		updatedPost.Title = "Updated Regression Test Post"
		updatedPost.Content = "This is updated content for regression testing."

		resp, err := suite.makeRequest("PUT", fmt.Sprintf("/api/blog-posts/%d", testPost.ID), updatedPost)
		assert.NoError(t, err)
		defer resp.Body.Close()

		assert.Equal(t, 200, resp.StatusCode, "Should update blog post successfully")

		// Verify file update
		suite.verifyFileContent(testPost.Filename, updatedPost.Title, updatedPost.Content)
	})

	// Test 4: List BlogPosts
	suite.T().Run("List BlogPosts", func(t *testing.T) {
		resp, err := suite.makeRequest("GET", "/api/blog-posts", nil)
		assert.NoError(t, err)
		defer resp.Body.Close()

		assert.Equal(t, 200, resp.StatusCode, "Should list blog posts successfully")

		var posts []BlogPost
		err = json.NewDecoder(resp.Body).Decode(&posts)
		assert.NoError(t, err)
		assert.NotEmpty(t, posts, "Should return at least one blog post")

		// Find our test post
		found := false
		for _, post := range posts {
			if post.ID == testPost.ID {
				found = true
				break
			}
		}
		assert.True(t, found, "Should find our test post in the list")
	})

	// Test 5: Delete BlogPost
	suite.T().Run("Delete BlogPost", func(t *testing.T) {
		resp, err := suite.makeRequest("DELETE", fmt.Sprintf("/api/blog-posts/%d", testPost.ID), nil)
		assert.NoError(t, err)
		defer resp.Body.Close()

		assert.Equal(t, 204, resp.StatusCode, "Should delete blog post successfully")

		// Verify file deletion
		suite.verifyFileDeleted(testPost.Filename)

		// Verify DB deletion
		resp, err = suite.makeRequest("GET", fmt.Sprintf("/api/blog-posts/%d", testPost.ID), nil)
		assert.NoError(t, err)
		defer resp.Body.Close()
		assert.Equal(t, 404, resp.StatusCode, "Should return 404 for deleted post")
	})
}

func (suite *RegressionTestSuite) TestFilamentAdminAccess() {
	suite.T().Run("Filament Admin Access", func(t *testing.T) {
		resp, err := suite.makeRequest("GET", "/admin/blog-posts", nil)
		assert.NoError(t, err)
		defer resp.Body.Close()

		// Should redirect to login or show admin page
		assert.True(t, resp.StatusCode == 200 || resp.StatusCode == 302, 
			"Should access admin page or redirect to login")
	})
}

func (suite *RegressionTestSuite) verifyFileExists(filename string) {
	// Assuming blog files are stored in storage/app/blog/posts/
	filePath := filepath.Join("../../storage/app/blog/posts", filename)
	_, err := os.Stat(filePath)
	assert.NoError(suite.T(), err, "Blog post file should exist: %s", filePath)
}

func (suite *RegressionTestSuite) verifyFileContent(filename, expectedTitle, expectedContent string) {
	filePath := filepath.Join("../../storage/app/blog/posts", filename)
	content, err := os.ReadFile(filePath)
	assert.NoError(suite.T(), err, "Should read blog post file")

	fileContent := string(content)
	assert.Contains(suite.T(), fileContent, expectedTitle, "File should contain updated title")
	assert.Contains(suite.T(), fileContent, expectedContent, "File should contain updated content")
	assert.Contains(suite.T(), fileContent, "---", "File should contain YAML front matter")
}

func (suite *RegressionTestSuite) verifyFileDeleted(filename string) {
	filePath := filepath.Join("../../storage/app/blog/posts", filename)
	_, err := os.Stat(filePath)
	assert.True(suite.T(), os.IsNotExist(err), "Blog post file should be deleted: %s", filePath)
}

func (suite *RegressionTestSuite) TestDatabaseFileSynchronization() {
	suite.T().Run("Database File Synchronization", func(t *testing.T) {
		// Create a test post
		testPost := BlogPost{
			Title:   "Sync Test Post",
			Slug:    "sync-test-post",
			Date:    time.Now().Format("2006-01-02"),
			Author:  "Sync Test Author",
			Content: "Testing database and file synchronization.",
		}

		// Create via API
		resp, err := suite.makeRequest("POST", "/api/blog-posts", testPost)
		assert.NoError(t, err)
		defer resp.Body.Close()

		var createdPost BlogPost
		err = json.NewDecoder(resp.Body).Decode(&createdPost)
		assert.NoError(t, err)

		// Verify both DB and file exist
		suite.verifyFileExists(createdPost.Filename)

		// Read file content and verify YAML front matter
		filePath := filepath.Join("../../storage/app/blog/posts", createdPost.Filename)
		content, err := os.ReadFile(filePath)
		assert.NoError(t, err)

		fileContent := string(content)
		assert.Contains(t, fileContent, "title: "+testPost.Title)
		assert.Contains(t, fileContent, "slug: "+testPost.Slug)
		assert.Contains(t, fileContent, "author: "+testPost.Author)
		assert.Contains(t, fileContent, testPost.Content)

		// Clean up
		suite.makeRequest("DELETE", fmt.Sprintf("/api/blog-posts/%d", createdPost.ID), nil)
	})
}