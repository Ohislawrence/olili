// resources/js/Composables/useContentLoader.js
import { ref, computed } from 'vue'
import axios from 'axios'

export function useContentLoader() {
  const contentLoading = ref(false)
  const contentError = ref(null)
  const loadedTopics = ref({}) // Cache for loaded topics
  const activeTopicId = ref(null)

  const loadTopicContent = async (courseId, topicId) => {
    if (loadedTopics.value[topicId]) {
      // Content already loaded, just set as active
      activeTopicId.value = topicId
      return loadedTopics.value[topicId]
    }

    contentLoading.value = false
    contentError.value = null
    activeTopicId.value = topicId

    try {
      const response = await axios.get(`/student/api/courses/${courseId}/topics/${topicId}/content`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        params: {
          include: 'contents,quiz,module'
        }
      })

      const topicData = response.data.data

      // Cache the loaded topic
      loadedTopics.value[topicId] = {
        ...topicData,
        loadedAt: new Date().toISOString()
      }

      return topicData
    } catch (error) {
      contentError.value = error.response?.data?.message || 'Failed to load topic content'
      console.error('Error loading topic content:', error)
      throw error
    } finally {
      contentLoading.value = false
    }
  }

  const loadTopicDetails = async (courseId, topicId) => {
    return loadTopicContent(courseId, topicId)
  }

  const loadQuizDetails = async (courseId, topicId) => {
    try {
      const response = await axios.get(`/student/api/courses/${courseId}/topics/${topicId}/quiz`, {
        headers: {
          'Accept': 'application/json'
        }
      })
      return response.data.data
    } catch (error) {
      console.error('Error loading quiz details:', error)
      throw error
    }
  }

  const loadProjectDetails = async (courseId, topicId) => {
    try {
      const response = await axios.get(`/student/api/courses/${courseId}/topics/${topicId}/project`, {
        headers: {
          'Accept': 'application/json'
        }
      })
      return response.data.data
    } catch (error) {
      console.error('Error loading project details:', error)
      throw error
    }
  }

  const generateContent = async (courseId, topicId) => {

    try {
      const response = await axios.post(`/student/api/courses/${courseId}/topics/${topicId}/generate-content`, {}, {
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })

      // Invalidate cache for this topic
      delete loadedTopics.value[topicId]

      return response.data.data
    } catch (error) {
      console.error('Error generating content:', error)
      throw error
    } finally {

    }
  }

  const generateQuiz = async (courseId, topicId) => {
    try {
      const response = await axios.post(`/student/api/courses/${courseId}/topics/${topicId}/generate-quiz`, {}, {
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })

      // Invalidate quiz cache for this topic
      const topicData = loadedTopics.value[topicId]
      if (topicData) {
        topicData.quiz = response.data.data
      }

      return response.data.data
    } catch (error) {
      console.error('Error generating quiz:', error)
      throw error
    }
  }

  const clearCache = (topicId = null) => {
    if (topicId) {
      delete loadedTopics.value[topicId]
    } else {
      loadedTopics.value = {}
    }
  }

  const isTopicLoaded = (topicId) => {
    return !!loadedTopics.value[topicId]
  }

  const getCachedTopic = (topicId) => {
    return loadedTopics.value[topicId]
  }

  return {
    contentError,
    loadedTopics,
    activeTopicId,
    loadTopicContent,
    loadTopicDetails,
    loadQuizDetails,
    loadProjectDetails,
    generateContent,
    generateQuiz,
    clearCache,
    isTopicLoaded,
    getCachedTopic
  }
}
