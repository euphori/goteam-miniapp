<template>
  <div class="h-screen bg-gray-100 p-6">
    <!-- Board Title -->
    <div class="flex justify-between items-center mb-6">
      <div class="flex items-center">
        <input
          v-if="editingTitle"
          v-model="boardTitle"
          @blur="updateBoardTitle"
          @keydown.enter="updateBoardTitle"
          class="text-2xl font-bold border-b-2 border-gray-500 focus:outline-none bg-transparent"
          autofocus
        />
        <h1 v-else class="text-2xl font-bold cursor-pointer" @click="editingTitle = true">
          {{ boardTitle }}
        </h1>
      </div>
      <button @click="goBack" class="text-gray-600 text-sm hover:underline">Back</button>
    </div>

    <!-- Lists Container -->
    <div class="flex-1 overflow-x-auto overflow-y-hidden px-4 pb-2 h-[calc(100vh-128px)]">
      <div class="flex gap-4 w-max items-start">
        <!-- Individual Lists -->
        <div
          v-for="list in lists"
          :key="list.id"
          class="bg-gray-200 w-64 p-4 rounded shadow-md flex-shrink-0 min-h-fit"
        >
          <!-- List Title -->
          <div class="flex justify-between items-center mb-2">
            <h3 class="text-lg font-semibold">{{ list.title }}</h3>
            <button @click="removeList(list.id)" class="text-gray-800 hover:text-red-600 text-xs">
              ✕
            </button>
          </div>

          <!-- Cards Inside List (Auto height based on content) -->
          <div class="space-y-2 overflow-y-auto max-h-[400px]">
            <div
              v-for="card in list.cards"
              :key="card.id"
              class="bg-white p-2 rounded border-b border-gray-400 cursor-pointer hover:bg-gray-300 relative"
            >
              <!-- Delete Button -->
              <button
                @click="deleteCard(list.id, card.id)"
                class="absolute top-1 right-1 text-gray-500 hover:text-red-600 text-xs"
              >
                ✕
              </button>
              {{ card.text }}
            </div>

            <!-- Add Card Button -->
            <button
              v-if="!showAddCard[list.id]"
              @click="showAddCard[list.id] = true"
              class="w-full p-2 bg-gray-300 rounded hover:bg-gray-400 text-left"
            >
              + Add a card
            </button>

            <!-- Input for adding a card -->
            <div v-if="showAddCard[list.id]" class="space-y-2">
              <input
                v-model="newCardText[list.id]"
                placeholder="Enter card title..."
                class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                @keydown.enter="addCard(list.id)"
                @blur="handleCardInputBlur(list.id)"
                autofocus
              />

              <div class="flex space-x-2">
                <button
                  @click="addCard(list.id)"
                  class="add-card-button px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                >
                  Add Card
                </button>
                <button
                  @click="((showAddCard[list.id] = false), (newCardText[list.id] = ''))"
                  class="px-4 py-2 bg-gray-200 text-gray-600 rounded hover:bg-gray-300"
                >
                  Cancel
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Create List Button -->
        <button
          v-if="!showAddListForm"
          @click="showAddListForm = true"
          class="w-64 text-left px-4 py-2 text-white bg-gray-500 rounded hover:bg-gray-600 opacity-70"
        >
          + Add another list
        </button>

        <!-- Add List Form -->
        <div
          v-if="showAddListForm"
          class="p-2 bg-gray-100 rounded-lg shadow-md flex-shrink-0 w-64 h-40 self-center"
        >
          <form @submit.prevent="addList" class="flex flex-col space-y-2">
            <textarea
              v-model="newListTitle"
              class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Enter list name..."
              maxlength="512"
            ></textarea>
            <div class="flex space-x-2">
              <button
                type="submit"
                class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600"
              >
                Add list
              </button>
              <button
                type="button"
                @click="closeAddListForm"
                class="px-4 py-2 text-gray-500 bg-gray-200 rounded hover:bg-gray-300"
              >
                Cancel
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/api'

const route = useRoute()
const router = useRouter()

const boardId = ref(route.params.id)
const boardTitle = ref('Untitled Board')
const lists = ref([])
const newListTitle = ref('')
const showAddListForm = ref(false)
const editingTitle = ref(false)

// Track new card text for each list
const newCardText = ref({})
const showAddCard = ref({})

// Watch for route changes and reload lists
watch(
  () => route.params.id,
  (newId) => {
    boardId.value = newId
    fetchBoard()
    fetchLists()
  },
)

// Fetch the board title from backend
const fetchBoard = async () => {
  try {
    const response = await api.get(`/api/boards/${boardId.value}`)
    boardTitle.value = response.data.title || 'Untitled Board'
  } catch (error) {
    console.error('Error fetching board:', error)
  }
}

// Fetch lists from the backend
const fetchLists = async () => {
  lists.value = [] // Clear lists before fetching
  console.log('Fetching lists for board:', boardId.value)

  try {
    const response = await api.get(`/api/lists?board_id=${boardId.value}`)
    console.log('Fetched lists:', response.data)
    lists.value = response.data
  } catch (error) {
    console.error('Error fetching lists:', error)
  }
}

// Update board title in the backend
const updateBoardTitle = async () => {
  editingTitle.value = false
  if (!boardTitle.value.trim()) {
    boardTitle.value = 'Untitled Board'
    return
  }

  try {
    await api.put(`/api/boards/${boardId.value}`, { title: boardTitle.value })
  } catch (error) {
    console.error('Error updating board title:', error)
  }
}

// Load board title and lists when component mounts
onMounted(() => {
  fetchBoard()
  fetchLists()
})

// Navigate back to dashboard
const goBack = () => {
  router.push('/')
}

// Add a new list
const addList = async () => {
  if (!newListTitle.value.trim()) return

  try {
    const response = await api.post('/api/lists', {
      title: newListTitle.value,
      board_id: boardId.value,
    })

    // Ensure the new list has a 'cards' array
    const newList = { ...response.data, cards: [] }

    lists.value.push(newList)
    newCardText.value[newList.id] = '' // Initialize newCardText
    newListTitle.value = ''
    showAddListForm.value = false
  } catch (error) {
    console.error('Error adding list:', error)
  }
}

// Remove a list
const removeList = async (listId) => {
  try {
    await api.delete(`/api/lists/${listId}`)
    lists.value = lists.value.filter((list) => list.id !== listId)
    delete newCardText.value[listId] // Clean up state
  } catch (error) {
    console.error('Error deleting list:', error)
  }
}

// Add card when Enter is pressed
const addCard = async (listId) => {
  const text = newCardText.value[listId]?.trim()
  if (!text) return

  try {
    const response = await api.post(`/api/todo-lists/${listId}/cards`, {
      text,
      to_do_list_id: listId,
    })

    const list = lists.value.find((list) => list.id === listId)
    if (list) {
      list.cards.push(response.data)
    }
    newCardText.value[listId] = '' // Clear input field after adding card
  } catch (error) {
    console.error('Error adding card:', error)
  }
}

const deleteCard = async (listId, cardId) => {
  try {
    await api.delete(`/api/cards/${cardId}`)
    const list = lists.value.find((list) => list.id === listId)
    if (list) {
      list.cards = list.cards.filter((card) => card.id !== cardId)
    }
  } catch (error) {
    console.error('Error deleting card:', error)
  }
}

const handleCardInputBlur = (event, listId) => {
  if (event.relatedTarget && event.relatedTarget.classList.contains('add-card-button')) {
    return
  }

  addCard(listId)
  showAddCard.value[listId] = false // Hide the input field after adding
}
</script>
